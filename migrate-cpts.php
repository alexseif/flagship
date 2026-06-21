<?php
// Ensure WordPress environment is loaded when running this file via wp eval-file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "Starting Neo Fos Migration...\n";

// 1. Neo Fos Migration (from the single page "αλεξανδρινός-ταχυδρόμος")
$page_query = new WP_Query( array(
    'name'           => 'αλεξανδρινός-ταχυδρόμος',
    'post_type'      => 'page',
    'posts_per_page' => 1,
) );

$migrated_neo_fos = 0;
if ( $page_query->have_posts() ) {
    $page_query->the_post();
    $content = get_the_content();
    
    // Match all <a href="...pdf">...</a> links
    // The anchor text will be used as the title.
    if ( preg_match_all( '/<a\s+[^>]*href=["\']([^"\']+\.pdf)["\'][^>]*>(.*?)<\/a>/is', $content, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            $pdf_url = $match[1];
            $title = strip_tags( $match[2] );
            $title = trim( $title );
            if ( empty( $title ) ) {
                $title = basename( $pdf_url );
            }

            $new_post = array(
                'post_title'   => $title,
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'neo_fos',
                'post_date'    => get_the_date( 'Y-m-d H:i:s' ),
            );
            
            $new_post_id = wp_insert_post( $new_post );
            if ( $new_post_id && ! is_wp_error( $new_post_id ) ) {
                update_post_meta( $new_post_id, 'pdf_attachment_link', $pdf_url );
                $migrated_neo_fos++;
                echo "Migrated Neo Fos: $title -> PDF: $pdf_url\n";
            }
        }
    } else {
        echo "No PDF links found in the page content.\n";
    }
    wp_reset_postdata();
} else {
    echo "Page 'αλεξανδρινός-ταχυδρόμος' not found.\n";
}
echo "Finished Neo Fos Migration. Migrated: $migrated_neo_fos\n\n";

echo "Starting Board Members Migration...\n";

// 2. Board Members Migration
$testimonials_query = new WP_Query( array(
    'post_type'      => 'testimonial',
    'posts_per_page' => -1,
    'post_status'    => 'any',
) );

$migrated_board = 0;
if ( $testimonials_query->have_posts() ) {
    while ( $testimonials_query->have_posts() ) {
        $testimonials_query->the_post();
        
        $new_post = array(
            'post_title'   => get_the_title(),
            'post_content' => get_the_content(), // Might contain WPBakery shortcodes, but Task 8 will clean them
            'post_status'  => get_post_status(),
            'post_type'    => 'board_member',
            'post_date'    => get_the_date( 'Y-m-d H:i:s' ),
        );
        
        $new_post_id = wp_insert_post( $new_post );
        if ( $new_post_id && ! is_wp_error( $new_post_id ) ) {
            // Set thumbnail
            $thumb_id = get_post_thumbnail_id( get_the_ID() );
            if ( $thumb_id ) {
                set_post_thumbnail( $new_post_id, $thumb_id );
            }
            
            // Trash original
            wp_trash_post( get_the_ID() );
            $migrated_board++;
            echo "Migrated Board Member: " . get_the_title() . "\n";
        }
    }
    wp_reset_postdata();
}
echo "Finished Board Members Migration. Migrated: $migrated_board\n";
