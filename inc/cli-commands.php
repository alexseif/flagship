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
}

WP_CLI::add_command( 'eka', 'EKA_CLI' );
