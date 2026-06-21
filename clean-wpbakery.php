<?php
// Ensure WordPress environment is loaded when running this file via wp eval-file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "Starting WPBakery Shortcode Cleanup...\n";

// Query all posts, pages, and other post types that might have WPBakery shortcodes
$query = new WP_Query( array(
    'post_type'      => 'any',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    's'              => '[vc_',
) );

$cleaned_count = 0;
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        $content = get_the_content();
        
        // Remove [vc_*] and [/vc_*] tags, preserving the content inside them
        $clean_content = preg_replace( '/\[\/?vc_[^\]]+\]/i', '', $content );
        
        // Trim extra whitespace left by removed tags
        $clean_content = trim( $clean_content );
        
        // Only update if it actually changed
        if ( $clean_content !== $content ) {
            wp_update_post( array(
                'ID'           => get_the_ID(),
                'post_content' => $clean_content,
            ) );
            $cleaned_count++;
            echo "Cleaned ID " . get_the_ID() . ": " . get_the_title() . "\n";
        }
    }
    wp_reset_postdata();
}

echo "Finished cleanup. Total posts cleaned: $cleaned_count\n";
