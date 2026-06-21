<?php
// Ensure WordPress environment is loaded when running this file via wp eval-file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'pll_set_post_language' ) ) {
    echo "Polylang is not active or function not found.\n";
    exit;
}

$query = new WP_Query( array(
    'post_type'      => array( 'neo_fos', 'board_member' ),
    'posts_per_page' => -1,
    'post_status'    => 'any',
) );

$count = 0;
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        $post_id = get_the_ID();
        
        // Assign to Greek
        pll_set_post_language( $post_id, 'el' );
        $count++;
    }
    wp_reset_postdata();
}

echo "Assigned Greek language to $count posts.\n";
