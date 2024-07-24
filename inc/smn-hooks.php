<?php
/**
 * Custom hooks.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_filter( 'wpcf7_form_tag', 'smn_wpcf7_form_control_class', 10, 2 );
function smn_wpcf7_form_control_class( $scanned_tag, $replace ) {

   $excluded_types = array(
        'acceptance',
        'checkbox',
        'radio',
   );

   if ( in_array( $scanned_tag['type'], $excluded_types ) ) return $scanned_tag;

   switch ($scanned_tag['type']) {
    case 'submit':
        $scanned_tag['options'][] = 'class:btn';
        $scanned_tag['options'][] = 'class:btn-dark';
        break;
    
    default:
        $scanned_tag['options'][] = 'class:form-control';
        break;
   }
   
   return $scanned_tag;
}

// Add body classes
add_filter( 'body_class', 'smn_add_page_body_class' );
function smn_add_page_body_class( $classes ) {
    if ( is_page() && !is_front_page() ) {
        $classes[] = 'padded-bottom-page';
    }
    if ( is_active_sidebar( 'carousel-ad' ) ) {
        $classes[] = 'has-ticker';
    }

    return $classes;
}


add_action( 'wp_body_open', 'top_anchor');
function top_anchor() {
    echo '<div id="top">';
}


add_action( 'wp_footer', 'smn_show_brand_stamps_dropup' );
function smn_show_brand_stamps_dropup() {

    smn_brand_stamps_dropup();
    
}

add_action( 'wp_footer', 'smn_carousel_ad' );
function smn_carousel_ad() {
    
    if ( is_active_sidebar( 'carousel-ad' ) ) {
        
        add_filter( 'acf/settings/current_language',  '__return_false' );

        $post_id = get_field( 'footer_ticker_link_post_id', 'option' );
        $term_id = get_field( 'footer_ticker_link_term_id', 'option' );

        // add_filter( 'acf/settings/current_language',  '__return_true' );
        
        if ( $post_id ) {
            $link = get_permalink( $post_id );
        } elseif( $term_id ) {
            $link = get_term_link( $term_id );
        } else {
            $link = false;
        }


        if ( $link ) {
            echo '<a class="footer-ticker-link fixed-bottom" id="wrapper-carousel-ad" href="' . esc_url( $link ) . '">';
        } else {
            echo '<div class="fixed-bottom" id="wrapper-carousel-ad">';
        }
        
            echo '<div id="footer-ticker" class="ticker">';
                dynamic_sidebar( 'carousel-ad' );
            echo '</div>';

        if ( $link ) {            
            echo '</a>';
        } else {
            echo '</div>';
        }    
        ?>

    <?php }

}

add_action( 'wp_footer', 'smn_fixed_right_sidebar' );
function smn_fixed_right_sidebar() {
    
    if ( is_active_sidebar( 'fixed-right' ) ) {

        echo '<div id="fixed-right-sidebar">';

            dynamic_sidebar( 'fixed-right' );

        echo '</div>';

    }

}

function author_page_redirect() {
    if ( is_author() ) {
        wp_redirect( home_url() );
    }
}
add_action( 'template_redirect', 'author_page_redirect' );

function es_blog() {

    if( is_singular('post') || is_category() || is_tag() || ( is_home() && !is_front_page() ) ) {
        return true;
    }

    return false;
}

add_filter( 'theme_mod_understrap_sidebar_position', 'cargar_sidebar');
function cargar_sidebar( $valor ) {
    if ( es_blog() ) {
        $valor = 'right';
    } else {
        $valor = 'none';
    }
    return $valor;
}


add_filter( 'understrap_site_info_content', function($site_info) {

    return do_shortcode( $site_info );

});

function smn_change_store_permalink( $permalink, $post, $leavename ) {
   
    if ( 'tienda' != $post->post_type ) return $permalink;

    $permalink = smn_get_store_google_url();
    return $permalink;
}
add_filter('post_link', 'smn_change_store_permalink', 10, 3);


add_filter('icl_ls_languages', 'modify_language_switcher');
function modify_language_switcher($languages) {
    $current_language = apply_filters( 'wpml_current_language', NULL );
    if (isset($languages[$current_language])) {
        $languages[$current_language]['native_name'] = substr($languages[$current_language]['native_name'], 0, 2);
    }
    return $languages;
}

// Allows to translate the title of a custom menu item (avoiding the use of WPML menu sync functionality)
add_filter( 'wp_nav_menu_objects', 'smn_custom_menu_item_title', 10, 2 );
function smn_custom_menu_item_title( $items, $args ) {
    foreach ( $items as &$item ) {

        if ( 'monta-tu-locopolo' === sanitize_title( $item->title ) ) {
            $item->title = __( 'Monta tu LOCOPOLO', 'smn' );
        }

    }

    return $items;
}