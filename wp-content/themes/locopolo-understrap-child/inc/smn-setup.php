<?php
/**
 * Theme basic setup.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

add_action( 'after_setup_theme', 'smn_setup' );
function smn_setup() {

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
        'legal'         => __( 'Información legal', 'smn-admin' ),
		'botones'         => __( 'Menú botones', 'smn-admin' ),
	) );

}

add_filter( 'wp_trim_excerpt', 'understrap_all_excerpts_get_more_link' );
function understrap_all_excerpts_get_more_link( $post_excerpt ) {
	if ( ! is_admin() ) {
        global $post;
        if ( !$post->post_excerpt ) $post_excerpt .= '...';
    }
	return $post_excerpt;
}

// add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
function custom_excerpt_length( $length ) {
     return 25;
}

add_filter( 'get_the_archive_title', 'prefix_category_title' );
function prefix_category_title( $title ) {
    if ( is_tax() || is_category() || is_tag() ) {
        $title = single_term_title( '', false );
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    }
    return $title;
}

add_action( 'pre_get_posts', 'smn_pre_get_posts' );
function smn_pre_get_posts($query) {
    if (!$query->is_main_query() || is_admin() ) return;

    if (is_search()) {
        $query->set('posts_per_page', 30);
    }
}