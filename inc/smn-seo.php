<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Add SEO meta tags
add_action( 'wp_head', 'smn_add_seo_meta_tags' );
function smn_add_seo_meta_tags() {

	$seo_description = '';
	$seo_image = '';
	$seo_image_alt = '';

	if ( is_singular() ) {
		global $post;
		$seo_description = wp_trim_words( $post->post_content, 100 );
		$seo_image = get_the_post_thumbnail_url( $post, 'full' );
		$seo_image_alt = get_post_meta( get_post_thumbnail_id( $post ), '_wp_attachment_image_alt', true );
	}

	if ( $seo_description ) {
		echo '<meta name="description" content="' . $seo_description . '">';
	}

	if ( $seo_image ) {
		echo '<meta property="og:image" content="' . $seo_image . '">';
	}

	if ( $seo_image_alt ) {
		echo '<meta property="og:image:alt" content="' . $seo_image_alt . '">';
	}
}

// Set image alt attribute from title if image alt is empty
add_filter( 'wp_get_attachment_image_attributes', 'smn_set_image_alt_from_title', 10, 3 );
function smn_set_image_alt_from_title( $attr, $attachment, $size ) {
	if ( empty( $attr['alt'] ) ) {
		$attr['alt'] = get_the_title( $attachment->ID );
	}
	return $attr;
}