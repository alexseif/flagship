<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

class EKA_CLI {
    /**
     * Migrate Alexandrinos Tachydromos from legacy DB
     *
     * @subcommand migrate-tachydromos
     */
    public function migrate_tachydromos() {
        WP_CLI::line('Connecting to legacy DB...');
        $legacy_db = new wpdb('root', '0024', 'db207080_eka', 'localhost');
        if ($legacy_db->error) {
            WP_CLI::error('Could not connect to db207080_eka.');
        }

        $page = $legacy_db->get_row("SELECT ID, post_content FROM wp_posts WHERE post_title LIKE '%Ταχυδρόμος%' AND post_status = 'publish' AND post_type = 'page'");
        if (!$page) {
            WP_CLI::error('Legacy page not found.');
        }

        preg_match_all('/<a[^>]+href=["\']([^"\']+\.pdf)["\'][^>]*>(.*?)<\/a>/is', $page->post_content, $matches, PREG_SET_ORDER);
        
        $items = [];
        foreach ($matches as $match) {
            $pdf_url = $match[1];
            $inner = $match[2];
            $title = trim(strip_tags($inner));
            
            if (!isset($items[$pdf_url])) {
                $items[$pdf_url] = ['title' => '', 'img_url' => ''];
            }
            if ($title) {
                $items[$pdf_url]['title'] = $title;
            }
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/is', $inner, $img_match)) {
                $items[$pdf_url]['img_url'] = $img_match[1];
            }
        }

        global $wpdb;

        foreach ($items as $pdf_url => $data) {
            // Find PDF in local db
            $pdf_basename = wp_basename($pdf_url);
            $pdf_attachment = $wpdb->get_row($wpdb->prepare("SELECT ID, post_date FROM {$wpdb->posts} WHERE post_type='attachment' AND guid LIKE %s", '%' . $wpdb->esc_like($pdf_basename)));
            
            if (!$pdf_attachment) {
                WP_CLI::warning("PDF not found in current DB: $pdf_basename");
                continue;
            }

            $pdf_id = $pdf_attachment->ID;
            $post_date = $pdf_attachment->post_date;
            $title = $data['title'] ? $data['title'] : wp_basename($pdf_url, '.pdf');

            // Find image attachment if any
            $thumbnail_id = 0;
            if ($data['img_url']) {
                $img_basename = wp_basename($data['img_url']);
                // Strip dimension suffix e.g. -724x1024
                $img_clean = preg_replace('/-\d+x\d+(\.[a-zA-Z]+)$/', '$1', $img_basename);
                $img_attachment = $wpdb->get_row($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment' AND guid LIKE %s", '%' . $wpdb->esc_like($img_clean)));
                if ($img_attachment) {
                    $thumbnail_id = $img_attachment->ID;
                }
            }

            // Check idempotency
            $existing = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='pdf_file' AND meta_value=%d", $pdf_id));
            if ($existing) {
                WP_CLI::line("Skipping existing: $title");
                continue;
            }

            // Insert post
            $post_id = wp_insert_post([
                'post_title' => $title,
                'post_status' => 'publish',
                'post_type' => 'alx_tachydromos',
                'post_date' => $post_date,
            ]);

            if (is_wp_error($post_id)) {
                WP_CLI::warning("Failed to insert: $title");
                continue;
            }

            update_field('pdf_file', $pdf_id, $post_id);

            if ($thumbnail_id) {
                set_post_thumbnail($post_id, $thumbnail_id);
            }

            WP_CLI::success("Migrated: $title");
        }
        
        WP_CLI::success('Tachydromos migration complete.');
    }

    /**
     * Migrate Board Members from legacy DB
     *
     * @subcommand migrate-board
     */
    public function migrate_board() {
        WP_CLI::line('Connecting to legacy DB...');
        $legacy_db = new wpdb('root', '0024', 'db207080_eka', 'localhost');
        if ($legacy_db->error) {
            WP_CLI::error('Could not connect to db207080_eka.');
        }

        // Fetch testimonials
        $testimonials = $legacy_db->get_results("SELECT ID, post_title, post_content, menu_order FROM wp_posts WHERE post_type='testimonial' AND post_status='publish'");

        // Fetch post languages
        $languages = $legacy_db->get_results("
            SELECT tr.object_id, t.slug as language
            FROM wp_term_relationships tr
            JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE tt.taxonomy = 'language'
        ");
        $post_languages = [];
        foreach ($languages as $l) {
            $post_languages[$l->object_id] = $l->language;
        }

        // Fetch translation groups
        $translations = $legacy_db->get_results("
            SELECT t.description as serialized_group
            FROM wp_term_taxonomy tt
            JOIN wp_terms t ON tt.term_id = t.term_id
            WHERE tt.taxonomy = 'post_translations'
        ");
        
        $groups = [];
        foreach ($translations as $t) {
            $group = unserialize($t->serialized_group);
            if (is_array($group)) {
                $groups[] = $group;
            }
        }

        // Fetch legacy thumbnails
        $thumbnails = $legacy_db->get_results("
            SELECT post_id, meta_value as thumbnail_id
            FROM wp_postmeta
            WHERE meta_key='_thumbnail_id'
        ");
        $legacy_thumbs = [];
        foreach ($thumbnails as $t) {
            $legacy_thumbs[$t->post_id] = $t->thumbnail_id;
        }

        global $wpdb;

        $migrated_map = []; // legacy ID => new ID

        // Process Greek (el) first as canonical
        foreach (['el', 'en', 'ar'] as $lang) {
            foreach ($testimonials as $t) {
                $post_lang = isset($post_languages[$t->ID]) ? $post_languages[$t->ID] : 'el'; // fallback to el
                if ($post_lang !== $lang) continue;

                // Check idempotency (assuming post_title is unique enough, or using meta)
                $existing = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_legacy_testimonial_id' AND meta_value=%d", $t->ID));
                if ($existing) {
                    $migrated_map[$t->ID] = $existing;
                    WP_CLI::line("Skipping existing: {$t->post_title} ($lang)");
                    continue;
                }

                // Find thumbnail in current DB by filename
                $thumbnail_id = 0;
                if (isset($legacy_thumbs[$t->ID])) {
                    $legacy_thumb_id = $legacy_thumbs[$t->ID];
                    // Get legacy attachment file
                    $legacy_attachment = $legacy_db->get_row($legacy_db->prepare("SELECT meta_value FROM wp_postmeta WHERE post_id=%d AND meta_key='_wp_attached_file'", $legacy_thumb_id));
                    if ($legacy_attachment) {
                        $filename = wp_basename($legacy_attachment->meta_value);
                        // Find in current db
                        $current_attachment = $wpdb->get_row($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s", '%' . $wpdb->esc_like($filename)));
                        if ($current_attachment) {
                            $thumbnail_id = $current_attachment->post_id;
                        }
                    }
                }

                // Insert post
                $post_id = wp_insert_post([
                    'post_title' => $t->post_title,
                    'post_content' => $t->post_content,
                    'post_status' => 'publish',
                    'post_type' => 'board_member',
                    'menu_order' => $t->menu_order,
                ]);

                if (is_wp_error($post_id)) {
                    WP_CLI::warning("Failed to insert: {$t->post_title}");
                    continue;
                }

                $migrated_map[$t->ID] = $post_id;
                update_post_meta($post_id, '_legacy_testimonial_id', $t->ID);
                
                if ($thumbnail_id) {
                    set_post_thumbnail($post_id, $thumbnail_id);
                }

                // Set Polylang language
                if (function_exists('pll_set_post_language')) {
                    pll_set_post_language($post_id, $lang);
                }

                WP_CLI::success("Migrated ($lang): {$t->post_title}");
            }
        }

        // Link translations
        if (function_exists('pll_save_post_translations')) {
            foreach ($groups as $group) {
                // $group is ['el' => legacy_id, 'en' => legacy_id, 'ar' => legacy_id]
                $new_group = [];
                foreach ($group as $lang => $legacy_id) {
                    if (isset($migrated_map[$legacy_id])) {
                        $new_group[$lang] = $migrated_map[$legacy_id];
                    }
                }
                if (count($new_group) > 1) {
                    pll_save_post_translations($new_group);
                    WP_CLI::line("Linked translations: " . implode(', ', $new_group));
                }
            }
        }

        WP_CLI::success('Board Members migration complete.');
    }
}

WP_CLI::add_command( 'eka', 'EKA_CLI' );
