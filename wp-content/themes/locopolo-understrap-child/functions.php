<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'PIDE_YA_ID', apply_filters( 'wpml_object_id', 22, 'page', true ) );
define( 'TIENDA_OFICIAL_TERM_ID', apply_filters( 'wpml_object_id', 77, 'tipo-tienda', true ) );
define( 'HOME_TERM_ID', apply_filters( 'wpml_object_id', 6, 'product_cat', true ) );

// UnderStrap's includes directory.
$understrap_inc_dir = 'inc';

// Array of files to include.
$understrap_includes = array(
    '/smn-widgets.php',
    // '/smn-dummy-content.php',
    '/smn-seo.php',
    '/smn-setup.php',
    '/smn-hooks.php',
    // '/smn-customizer.php',
    '/smn-template-tags.php',
    '/smn-shortcodes.php',
    '/smn-blocks.php',
    '/smn-odoo-data.php',
);

// Load WooCommerce functions if WooCommerce is activated.
if ( class_exists( 'WooCommerce' ) ) {
    $understrap_includes[] = '/smn-woocommerce.php';
}

if ( class_exists( 'FacetWP' ) ) {
    $understrap_includes[] = '/smn-facetwp.php';
}

// Include files.
foreach ( $understrap_includes as $file ) {
    require_once get_theme_file_path( $understrap_inc_dir . $file );
}

// check if rank math or yoast seo are installed
if ( !class_exists( 'RankMath' ) && !class_exists( 'WPSEO_Admin' ) ) {
	require_once get_theme_file_path( $understrap_inc_dir . '/smn-seo.php' );
}

/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	wp_enqueue_style( 'slick', get_stylesheet_directory_uri() . '/js/slick/slick.css' );
	wp_enqueue_style( 'slick-theme', get_stylesheet_directory_uri() . '/js/slick/slick-theme.css' );

	// Get the theme data.
	$the_theme     = wp_get_theme();
	$theme_version = $the_theme->get( 'Version' );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";
	
	$css_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_styles );

    wp_enqueue_style( 'ticker', get_stylesheet_directory_uri() .'/css/eocjs-newsticker.css', null, '0.7.1' );
	wp_enqueue_style( 'locopolo-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $css_version );
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'slick', get_stylesheet_directory_uri() . '/js/slick/slick.min.js', null, null, false );
    wp_enqueue_script( 'ticker', get_stylesheet_directory_uri() .'/js/eocjs-newsticker.js', 'jquery', '0.7.1', false );
	
	$js_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_scripts );
	
	wp_enqueue_script( 'locopolo-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $js_version, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
    load_child_theme_textdomain( 'smn', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );


/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );
