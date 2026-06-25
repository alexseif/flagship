<?php
/**
 * Flagship Theme Functions
 */

function flagship_theme_setup() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'flagship_theme_setup');

function flagship_enqueue_assets() {
    // Enqueue Theme Components CSS (Assuming SCSS is compiled here)
    wp_enqueue_style(
        'flagship-components',
        get_template_directory_uri() . '/assets/css/theme-components.css',
        array(),
        wp_get_theme()->get('Version')
    );

    // Enqueue Header Scroll Interaction
    wp_enqueue_script(
        'flagship-header-scroll',
        get_template_directory_uri() . '/assets/js/header-scroll.js',
        array(),
        wp_get_theme()->get('Version'),
        true // Load in footer
    );

    // Enqueue Counters Observer
    wp_enqueue_script(
        'flagship-counters',
        get_template_directory_uri() . '/assets/js/counters.js',
        array(),
        wp_get_theme()->get('Version'),
        true // Load in footer
    );
}
add_action('wp_enqueue_scripts', 'flagship_enqueue_assets');

function flagship_register_block_styles() {
    register_block_style(
        'core/button',
        array(
            'name'  => 'swipe-hover',
            'label' => __( 'Swipe Interaction', 'flagship' ),
        )
    );
    register_block_style(
        'core/button',
        array(
            'name'  => 'elevate-hover',
            'label' => __( 'Elevated Elevation', 'flagship' ),
        )
    );
}
add_action('init', 'flagship_register_block_styles');

require_once get_theme_file_path( '/inc/custom-features.php' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once get_theme_file_path( '/inc/cli-commands.php' );
}
// Disable Contact Form 7 reCAPTCHA during development
add_filter( 'wpcf7_use_recaptcha_net', '__return_false' );
add_action( 'wp_print_scripts', function() {
    wp_dequeue_script( 'google-recaptcha' );
    wp_dequeue_script( 'wpcf7-recaptcha' );
}, 100 );
