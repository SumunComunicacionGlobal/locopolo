<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );

$terms = get_terms( array(
	'taxonomy' 	=> 'product_cat',
	'parent'	=> get_queried_object_id(),
) );

if ( ! $terms || is_wp_error( $terms ) ) {

	$terms = array( get_queried_object() );
	
}

foreach( $terms as $category ) {
?>

	<div class="hero-carousel">

		<div class="hero-carousel--inner">

			<?php get_template_part( 'global-templates/products-carousel', '', array( 'category' => $category ) ); ?>
		
		</div>

	</div>

<?php }

get_footer();