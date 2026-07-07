<?php
/**
 * Custom Post Types and Admin Features
 */

// Admin login panel branded
function ekalexandria_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo.png);
            width: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            padding-bottom: 30px;
        }
        body.login {
            background-color: #f5f5f5;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'ekalexandria_login_logo' );

// Greek admin dashboard labels for default posts
function ekalexandria_change_post_menu_label() {
    global $menu;
    global $submenu;
    if(isset($menu[5])) {
        $menu[5][0] = 'Νέα';
    }
    if(isset($submenu['edit.php'])) {
        $submenu['edit.php'][5][0] = 'Όλα τα Νέα';
        $submenu['edit.php'][10][0] = 'Προσθήκη Νέου';
    }
}
add_action( 'admin_menu', 'ekalexandria_change_post_menu_label' );

// Register Polylang Switcher Shortcode for FSE Header
function ekalexandria_polylang_shortcode() {
    if ( function_exists('pll_the_languages') ) {
        return '<ul class="polylang-switcher" style="display:flex; list-style:none; gap:10px; margin:0; padding:0; align-items:center;">' . pll_the_languages( array( 'echo' => 0, 'hide_current' => 0 ) ) . '</ul>';
    }
    return '';
}
add_shortcode( 'polylang_langswitcher', 'ekalexandria_polylang_shortcode' );

// Register Alexandrinos Tachydromos CPT
add_action('init', function() {
    register_post_type('alx_tachydromos', [
        'labels' => [
            'name' => 'Alexandrinos Tachydromos',
            'singular_name' => 'Tachydromos',
            'menu_name' => 'Tachydromos',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Tachydromos',
            'edit_item' => 'Edit',
            'new_item' => 'New',
            'view_item' => 'View',
            'search_items' => 'Search',
            'not_found' => 'No items found',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'αλεξανδρινός-ταχυδρόμος', 'with_front' => false],
        'menu_icon' => 'dashicons-media-document',
    ]);
});

// Register ACF fields for Tachydromos PDF
add_action('acf/init', function() {
    if( function_exists('acf_add_local_field_group') ):
        acf_add_local_field_group(array(
            'key' => 'group_tachydromos_pdf',
            'title' => 'Tachydromos PDF',
            'fields' => array(
                array(
                    'key' => 'field_tachydromos_pdf_file',
                    'label' => 'PDF File',
                    'name' => 'pdf_file',
                    'type' => 'file',
                    'return_format' => 'array',
                    'mime_types' => 'pdf',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'alx_tachydromos',
                    ),
                ),
            ),
        ));
    endif;
});

// Register Board Member CPT
add_action('init', function() {
    register_post_type('board_member', [
        'labels' => [
            'name' => 'Board Members',
            'singular_name' => 'Board Member',
            'menu_name' => 'Board Members',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes'],
        'rewrite' => ['slug' => 'board-members', 'with_front' => false],
        'menu_icon' => 'dashicons-groups',
    ]);
});

// Redirect single board members to the archive page
add_action('template_redirect', function() {
    if (is_singular('board_member')) {
        wp_redirect(get_post_type_archive_link('board_member'), 301);
        exit;
    }
});

// Exclude Tachydromos and include Board Member for Polylang
add_filter('pll_get_post_types', function($post_types, $is_settings) {
    if (isset($post_types['alx_tachydromos'])) {
        unset($post_types['alx_tachydromos']);
    }
    $post_types['board_member'] = 'board_member';
    return $post_types;
}, 10, 2);

// Shortcode for Tachydromos PDF Button
add_shortcode('tachydromos_pdf_button', function() {
    $pdf = get_field('pdf_file');
    if ($pdf && isset($pdf['url'])) {
        return '<div class="wp-block-button"><a href="' . esc_url($pdf['url']) . '" class="wp-block-button__link wp-element-button" target="_blank" rel="noopener noreferrer">Προβολή PDF / View PDF</a></div>';
    }
    return '';
});

// Auto-set featured image from PDF
add_action('acf/save_post', function($post_id) {
    if (get_post_type($post_id) === 'alx_tachydromos') {
        $pdf = get_field('pdf_file', $post_id);
        if ($pdf && isset($pdf['ID'])) {
            set_post_thumbnail($post_id, $pdf['ID']);
        }
    }
}, 20);
