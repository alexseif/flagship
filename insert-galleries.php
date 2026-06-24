<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

echo "Starting Gallery Placeholder Insertion...\n";

$galleries = [
    'Στελέχωση' => [],
    'Staff' => [],
    'العاملين' => [],
    'Κοινοτικό Εντευκτήριο' => [10328],
    'Κοιμητήρια' => [10329, 7667, 7668, 7669, 7670, 7671, 7672, 7673],
    'Συντήρηση Κοιμητηρίων' => [7935, 7936, 7937, 7938, 7939, 7940, 7941, 7942],
    'Μουσείο Μουσικής' => [7821, 7822, 7823],
    'Φυσικής' => [7813, 7814, 7815], // Assuming partial match or full name
];

foreach ( $galleries as $title => $ids ) {
    $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'page' AND post_status = 'publish' LIMIT 1", $title ) );
    
    if ( ! $post_id ) {
        $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title LIKE %s AND post_type = 'page' AND post_status = 'publish' LIMIT 1", '%' . $wpdb->esc_like( $title ) . '%' ) );
    }

    if ( $post_id ) {
        $content = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %d", $post_id ) );

        if ( strpos($content, 'wp:gallery') !== false ) {
            echo "Gallery already exists on page '$title'. Skipping.\n";
            continue;
        }

        $gallery_html = "<!-- wp:gallery {\"linkTo\":\"none\"} -->\n<figure class=\"wp-block-gallery has-nested-images columns-default is-cropped\">\n";
        
        foreach ( $ids as $id ) {
            $gallery_html .= "<!-- wp:image {\"id\":$id,\"sizeSlug\":\"large\",\"linkDestination\":\"none\"} -->\n";
            $gallery_html .= "<figure class=\"wp-block-image size-large\"><img src=\"\" alt=\"\" class=\"wp-image-$id\"/></figure>\n";
            $gallery_html .= "<!-- /wp:image -->\n";
        }
        
        $gallery_html .= "</figure>\n<!-- /wp:gallery -->\n";

        // Append to content
        $new_content = $content . "\n\n" . $gallery_html;

        $wpdb->update(
            $wpdb->posts,
            array( 'post_content' => $new_content ),
            array( 'ID' => $post_id ),
            array( '%s' ),
            array( '%d' )
        );

        echo "Inserted gallery on page: $title (ID: $post_id)\n";
    } else {
        echo "Page not found: $title\n";
    }
}

echo "Finished Gallery Placeholder Insertion.\n";
