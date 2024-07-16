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

// Add body classes if is_page
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

// add_action( 'wp_footer', 'back_to_top', 20 );
function back_to_top() {
    echo '<a href="#top" class="back-to-top"></a>';
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


        <!--
        <script>
    
            jQuery("#footer-ticker").eocjsNewsticker({
                speed: 20,
                divider: ' - '
            });
    
        </script>
        -->
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

// add_filter( 'parse_tax_query', 'smn_do_not_include_children_in_product_cat_archive' );
// function smn_do_not_include_children_in_product_cat_archive( $query ) {
//     if ( 
//         ! is_admin() 
//         && $query->is_main_query()
//         && $query->is_tax( 'product_cat' )
//     ) {
//         $query->tax_query->queries[0]['include_children'] = 0;
//     }
// }

add_filter( 'understrap_site_info_content', function($site_info) {

    return do_shortcode( $site_info );

});

function smn_change_store_permalink( $permalink, $post, $leavename ) {
   
    if ( 'tienda' != $post->post_type ) return $permalink;

    $permalink = smn_get_store_google_url();
    return $permalink;
}
add_filter('post_link', 'smn_change_store_permalink', 10, 3);

// add_filter( 'nav_menu_link_attributes', 'smn_allow_bootstrap_dropdown_link', 10, 4 );
function smn_allow_bootstrap_dropdown_link( $atts, $item, $args, $depth ) {

    if ( isset($atts['data-bs-toggle']) && 'dropdown' == $atts['data-bs-toggle'] ) {

        if ( 'post_type' == $item->type ) {
            $atts['href'] = get_permalink( $item->object_id );
        } elseif ( 'taxonomy' == $item->type ) {
            $atts['href'] = get_term_link( $item->object_id );
        }
    
    }

    return $atts;

}

add_filter('icl_ls_languages', 'modify_language_switcher');
function modify_language_switcher($languages) {
    $current_language = apply_filters( 'wpml_current_language', NULL );
    if (isset($languages[$current_language])) {
        $languages[$current_language]['native_name'] = substr($languages[$current_language]['native_name'], 0, 2);
    }
    return $languages;
}