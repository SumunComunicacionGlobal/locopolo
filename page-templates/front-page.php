<?php
/**
 * Template Name: Front Page
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header(); ?>

<div class="hero-carousel">

	<div class="hero-carousel--inner">

		<?php get_template_part( 'global-templates/subcategories-carousel' ); ?>

	</div>
	
</div>

<?php get_footer();
