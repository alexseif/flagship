<?php
/**
 * Custom Post Types and Admin Features
 */

// Register Neo Fos Custom Post Type
function ekalexandria_register_neo_fos_cpt() {
    $labels = array(
        'name'                  => _x( 'Νέο Φως', 'Post Type General Name', 'ekalexandria-flagship' ),
        'singular_name'         => _x( 'Νέο Φως', 'Post Type Singular Name', 'ekalexandria-flagship' ),
        'menu_name'             => __( 'Νέο Φως', 'ekalexandria-flagship' ),
        'name_admin_bar'        => __( 'Νέο Φως', 'ekalexandria-flagship' ),
        'archives'              => __( 'Αρχείο Νέο Φως', 'ekalexandria-flagship' ),
        'attributes'            => __( 'Ιδιότητες', 'ekalexandria-flagship' ),
        'parent_item_colon'     => __( 'Γονικό:', 'ekalexandria-flagship' ),
        'all_items'             => __( 'Όλα τα Νέο Φως', 'ekalexandria-flagship' ),
        'add_new_item'          => __( 'Προσθήκη νέου', 'ekalexandria-flagship' ),
        'add_new'               => __( 'Προσθήκη νέου', 'ekalexandria-flagship' ),
        'new_item'              => __( 'Νέο', 'ekalexandria-flagship' ),
        'edit_item'             => __( 'Επεξεργασία', 'ekalexandria-flagship' ),
        'update_item'           => __( 'Ενημέρωση', 'ekalexandria-flagship' ),
        'view_item'             => __( 'Προβολή', 'ekalexandria-flagship' ),
        'view_items'            => __( 'Προβολή στοιχείων', 'ekalexandria-flagship' ),
        'search_items'          => __( 'Αναζήτηση', 'ekalexandria-flagship' ),
        'not_found'             => __( 'Δεν βρέθηκε', 'ekalexandria-flagship' ),
        'not_found_in_trash'    => __( 'Δεν βρέθηκε στον Κάδο', 'ekalexandria-flagship' ),
        'featured_image'        => __( 'Επιλεγμένη εικόνα', 'ekalexandria-flagship' ),
        'set_featured_image'    => __( 'Ορισμός εικόνας', 'ekalexandria-flagship' ),
        'remove_featured_image' => __( 'Αφαίρεση εικόνας', 'ekalexandria-flagship' ),
        'use_featured_image'    => __( 'Χρήση ως εικόνας', 'ekalexandria-flagship' ),
        'insert_into_item'      => __( 'Εισαγωγή στο στοιχείο', 'ekalexandria-flagship' ),
        'uploaded_to_this_item' => __( 'Ανέβηκε σε αυτό', 'ekalexandria-flagship' ),
        'items_list'            => __( 'Λίστα στοιχείων', 'ekalexandria-flagship' ),
        'items_list_navigation' => __( 'Πλοήγηση λίστας', 'ekalexandria-flagship' ),
        'filter_items_list'     => __( 'Φιλτράρισμα λίστας', 'ekalexandria-flagship' ),
    );
    $args = array(
        'label'                 => __( 'Νέο Φως', 'ekalexandria-flagship' ),
        'description'           => __( 'Newsletter Ekalexandria', 'ekalexandria-flagship' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-email-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enable Gutenberg editor
    );
    register_post_type( 'neo_fos', $args );
}
add_action( 'init', 'ekalexandria_register_neo_fos_cpt', 0 );

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

// Add Custom RSS Feed for Neo Fos
function ekalexandria_custom_rss_feed() {
    add_feed( 'neo-fos', 'ekalexandria_render_neo_fos_feed' );
}
add_action( 'init', 'ekalexandria_custom_rss_feed' );

function ekalexandria_render_neo_fos_feed() {
    header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );
    
    $args = array(
        'post_type'      => 'neo_fos',
        'posts_per_page' => 10,
    );
    $query = new WP_Query( $args );
    
    echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?>';
    ?>
    <rss version="2.0"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:wfw="http://wellformedweb.org/CommentAPI/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
        xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    >
    <channel>
        <title><?php bloginfo_rss('name'); ?> - Νέο Φως</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo get_feed_build_date('r'); ?></lastBuildDate>
        <language><?php bloginfo_rss( 'language' ); ?></language>
        
        <?php while( $query->have_posts()) : $query->the_post(); ?>
            <item>
                <title><?php the_title_rss(); ?></title>
                <link><?php the_permalink_rss(); ?></link>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <dc:creator><![CDATA[<?php the_author(); ?>]]></dc:creator>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                <content:encoded><![CDATA[<?php the_content_feed('rss2'); ?>
                <?php 
                $pdf_link = get_post_meta( get_the_ID(), 'pdf_attachment_link', true );
                if ( $pdf_link ) {
                    echo '<p><a href="' . esc_url( $pdf_link ) . '">Κατεβάστε το PDF</a></p>';
                }
                ?>
                ]]></content:encoded>
            </item>
        <?php endwhile; wp_reset_postdata(); ?>
    </channel>
    </rss>
    <?php
}

// Register ACF Field Group for Neo Fos CPT
if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
	'key' => 'group_neo_fos_pdf',
	'title' => 'Neo Fos PDF Attachment',
	'fields' => array(
		array(
			'key' => 'field_pdf_attachment_link',
			'label' => 'PDF Attachment Link',
			'name' => 'pdf_attachment_link',
			'type' => 'url',
			'instructions' => 'Enter the direct URL to the PDF file for this issue.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => 'https://...',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'neo_fos',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 1,
));
endif;

// Register Board Member Custom Post Type
function ekalexandria_register_board_member_cpt() {
    $labels = array(
        'name'                  => _x( 'Μέλη ΔΣ', 'Post Type General Name', 'ekalexandria-flagship' ),
        'singular_name'         => _x( 'Μέλος ΔΣ', 'Post Type Singular Name', 'ekalexandria-flagship' ),
        'menu_name'             => __( 'Μέλη ΔΣ', 'ekalexandria-flagship' ),
        'all_items'             => __( 'Όλα τα Μέλη ΔΣ', 'ekalexandria-flagship' ),
        'add_new_item'          => __( 'Προσθήκη νέου Μέλους', 'ekalexandria-flagship' ),
        'add_new'               => __( 'Προσθήκη', 'ekalexandria-flagship' ),
        'edit_item'             => __( 'Επεξεργασία', 'ekalexandria-flagship' ),
        'update_item'           => __( 'Ενημέρωση', 'ekalexandria-flagship' ),
    );
    $args = array(
        'label'                 => __( 'Μέλος ΔΣ', 'ekalexandria-flagship' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-businessman',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enable Gutenberg editor
    );
    register_post_type( 'board_member', $args );
}
add_action( 'init', 'ekalexandria_register_board_member_cpt', 0 );

// Register Polylang Switcher Shortcode for FSE Header
function ekalexandria_polylang_shortcode() {
    if ( function_exists('pll_the_languages') ) {
        return '<ul class="polylang-switcher" style="display:flex; list-style:none; gap:10px; margin:0; padding:0; align-items:center;">' . pll_the_languages( array( 'echo' => 0, 'hide_current' => 0 ) ) . '</ul>';
    }
    return '';
}
add_shortcode( 'polylang_langswitcher', 'ekalexandria_polylang_shortcode' );

/**
 * Enable Polylang support for custom post types
 */
function ekalexandria_pll_cpt_support( $post_types, $is_settings ) {
    $post_types['board_member'] = 'board_member';
    $post_types['neo_fos'] = 'neo_fos';
    return $post_types;
}
add_filter( 'pll_get_post_types', 'ekalexandria_pll_cpt_support', 10, 2 );

if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
	'key' => 'group_neo_fos_pdf',
	'title' => 'Neo Fos PDF',
	'fields' => array(
		array(
			'key' => 'field_neo_fos_pdf_file',
			'label' => 'Upload PDF File',
			'name' => 'neo_fos_pdf_file',
			'type' => 'file',
			'instructions' => 'Upload the PDF for this issue. This will be used as the direct link.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'id',
			'library' => 'all',
			'min_size' => '',
			'max_size' => '',
			'mime_types' => 'pdf',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'neo_fos',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'side',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 1,
));
endif;

/**
 * Automatically set Neo Fos featured image from uploaded PDF
 */
add_action('acf/save_post', 'ekalexandria_sync_pdf_thumbnail', 20);
function ekalexandria_sync_pdf_thumbnail( $post_id ) {
    if ( get_post_type( $post_id ) !== 'neo_fos' ) return;
    
    $attachment_id = get_post_meta( $post_id, 'neo_fos_pdf_file', true );
    if ( $attachment_id && is_numeric( $attachment_id ) ) {
        set_post_thumbnail( $post_id, $attachment_id );
    }
}

/**
 * Shortcode to output Neo Fos PDF Link
 */
add_shortcode('neo_fos_pdf_link', 'ekalexandria_neo_fos_pdf_link_shortcode');
function ekalexandria_neo_fos_pdf_link_shortcode() {
    $post_id = get_the_ID();
    $pdf_id = get_post_meta($post_id, 'neo_fos_pdf_file', true);
    $pdf_url = '';
    
    if ( $pdf_id && is_numeric($pdf_id) ) {
        $pdf_url = wp_get_attachment_url($pdf_id);
    } else {
        $legacy = get_post_meta($post_id, 'pdf_attachment_link', true);
        if ($legacy && !is_numeric($legacy)) {
            $pdf_url = $legacy;
        }
    }
    
    if ( $pdf_url ) {
        return '<div class="wp-block-buttons" style="margin-top:2em;"><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url($pdf_url) . '" target="_blank">' . __('Λήψη / Προβολή PDF', 'ekalexandria-flagship') . '</a></div></div>';
    }
    return '';
}

/**
 * Shortcode to output Neo Fos Archive Dropdown Filter
 */
add_shortcode('neo_fos_archive_filter', 'ekalexandria_neo_fos_archive_filter_shortcode');
function ekalexandria_neo_fos_archive_filter_shortcode() {
    $args = array(
        'type'            => 'monthly',
        'format'          => 'option',
        'post_type'       => 'neo_fos',
        'show_post_count' => false,
        'echo'            => 0
    );
    $options = wp_get_archives($args);
    if ($options) {
        $select = '<select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">';
        $select .= '<option value="">' . __('Επιλέξτε Μήνα', 'ekalexandria-flagship') . '</option>';
        $select .= $options;
        $select .= '</select>';
        return '<div class="neo-fos-filter" style="margin-bottom:20px;">' . $select . '</div>';
    }
    return '';
}

