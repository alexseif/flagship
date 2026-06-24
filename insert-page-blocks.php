<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

echo "Starting Page Recoveries Insertion...\n";

// Navigation Menus to append
$nav_menus = [
    'Ίδρυση' => 70,
    'Establishment' => 3377,
    'تأسيس' => 3378,
    'Δράση' => 71,
    'Activities' => 3944,
    'الأنشطة' => 3945,
    'Υπηρεσίες' => 117,
    'Services' => 3707,
    'الخدمات' => 3716,
];

foreach ( $nav_menus as $title => $menu_id ) {
    $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'page' AND post_status = 'publish' LIMIT 1", $title ) );
    
    if ( $post_id ) {
        $content = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %d", $post_id ) );

        if ( strpos($content, 'wp:navigation') !== false ) {
            echo "Navigation already exists on page '$title'. Skipping.\n";
        } else {
            $nav_html = "<!-- wp:navigation {\"ref\":$menu_id,\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} /-->\n";
            $new_content = $content . "\n\n" . $nav_html;

            $wpdb->update(
                $wpdb->posts,
                array( 'post_content' => $new_content ),
                array( 'ID' => $post_id ),
                array( '%s' ),
                array( '%d' )
            );
            echo "Inserted Navigation (ID: $menu_id) on page: $title\n";
        }
    } else {
        echo "Page not found for Nav: $title\n";
    }
}

// Partner Logos placeholder
$links_page = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = 'Σύνδεσμοι' AND post_type = 'page' AND post_status = 'publish' LIMIT 1" );
if ( $links_page ) {
    $content = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %d", $links_page ) );
    if ( strpos($content, 'wp:gallery') === false ) {
        $gallery_html = "<!-- wp:gallery {\"linkTo\":\"none\"} -->\n<figure class=\"wp-block-gallery has-nested-images columns-default is-cropped\">\n<!-- wp:paragraph --><p>[Placeholder for Partner Logos]</p><!-- /wp:paragraph -->\n</figure>\n<!-- /wp:gallery -->\n";
        $wpdb->update( $wpdb->posts, array( 'post_content' => $content . "\n" . $gallery_html ), array( 'ID' => $links_page ) );
        echo "Inserted Logos placeholder on 'Σύνδεσμοι'\n";
    }
}

// Icons placeholder
$announcements_page = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = 'Ανακοινώσεις ΕΚΑ' AND post_type = 'page' AND post_status = 'publish' LIMIT 1" );
if ( $announcements_page ) {
    $content = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %d", $announcements_page ) );
    if ( strpos($content, 'wp:paragraph') === false ) {
        $icons_html = "<!-- wp:paragraph --><p>[Placeholder for Icons]</p><!-- /wp:paragraph -->\n";
        $wpdb->update( $wpdb->posts, array( 'post_content' => $content . "\n" . $icons_html ), array( 'ID' => $announcements_page ) );
        echo "Inserted Icons placeholder on 'Ανακοινώσεις ΕΚΑ'\n";
    }
}

echo "Finished Page Recoveries.\n";
