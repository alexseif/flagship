<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "Starting Revolution Slider Cleanup...\n";

$query = new WP_Query( array(
    'post_type'      => 'any',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    's'              => '[rev_slider',
) );

$cleaned_count = 0;
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        $content = get_the_content();
        
        // Let's replace [rev_slider] with a cover block if a featured image exists.
        // Otherwise, just remove it or use a fallback heading.
        $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
        $replacement = '';
        
        if ( $thumbnail_id ) {
            $img_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
            $replacement = '<!-- wp:cover {"url":"' . esc_url($img_url) . '","id":' . $thumbnail_id . ',"dimRatio":50,"overlayColor":"base","align":"full"} -->
<div class="wp-block-cover alignfull"><span aria-hidden="true" class="wp-block-cover__background has-base-background-color has-background-dim"></span><img class="wp-block-cover__image-background wp-image-' . $thumbnail_id . '" alt="" src="' . esc_url($img_url) . '" data-object-fit="cover"/>
<div class="wp-block-cover__inner-container">
<!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="wp-block-heading has-text-align-center">' . esc_html(get_the_title()) . '</h1>
<!-- /wp:heading -->
</div></div><!-- /wp:cover -->';
        } else {
            // Fallback generic cover or just a heading group
            $replacement = '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|x-large","bottom":"var:preset|spacing|x-large"}}},"backgroundColor":"foreground","textColor":"background","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--x-large);padding-bottom:var(--wp--preset--spacing--x-large)">
<!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="wp-block-heading has-text-align-center">' . esc_html(get_the_title()) . '</h1>
<!-- /wp:heading -->
</div><!-- /wp:group -->';
        }
        
        $clean_content = preg_replace( '/\[rev_slider[^\]]*\]/i', $replacement, $content );
        
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
