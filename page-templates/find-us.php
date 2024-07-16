<?php
/**
 * Template Name: Find Us
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
$facet = get_field( 'find_us_facet_id' );
if ( !$facet ) {
	$facet = 'buscar_provincia_esp';
}

$find_us_term_id = get_field( 'find_us_term_id' );
if ( !$find_us_term_id ) {
	$find_us_term_id = 11;
}
?>

<div class="wrapper" id="full-width-page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content">

		<div class="row">

			<div class="col-md-6 col-lg-4 pb-5">

				<?php
				the_title(
					'<span class="badge--wrapper"><h1 class="wp-block-heading badge">',
					'</h1></span>'
				);
				?>

				<div class="stores-map">
					<?php echo do_shortcode( '[mapa_establecimientos]' ); ?>
				</div>

				<?php smn_find_us_navigation(); ?>

			</div>

			<div class="col-md-6 col-lg-8 content-area" id="primary">

				<main class="site-main" id="main" role="main">

					<?php
					while ( have_posts() ) {
						the_post(); ?>

						<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

							<div class="entry-content">

								<?php
								the_content();
								understrap_link_pages();
								?>

							</div><!-- .entry-content -->

							<footer class="entry-footer">

								<?php understrap_edit_post_link(); ?>

							</footer><!-- .entry-footer -->

						</article><!-- #post-<?php the_ID(); ?> -->

					<?php } ?>

					<div class="stores-search-form ">

						<div class="row align-items-start">

							<div class="col-xl-7 stores-search-form-fields">
							
								<?php // echo do_shortcode( '[facetwp facet="buscar_ciudad"]' ); ?>
								<?php echo do_shortcode( '[facetwp facet="' . $facet . '"]' ); ?>
								<?php echo do_shortcode( '[facetwp facet="reset"]' ); ?>

							</div>

							<div class="col-xl-4 offset-xl-1 stores-search-form-results-summary">

								<?php echo do_shortcode( '[facetwp facet="contador_tiendas"]' ); ?>
								<?php echo wpautop( '<a class="results-map-link d-none" href="#" target="_blank" rel="noopener noreferrer">'. __( 'Ver en maps', 'smn' ) .'</a>' ); ?>
						
							</div>

						</div>

					</div>


					<?php
					$taxonomy = 'zona';

					$args = array(
						'post_type' => 'tienda',
						'posts_per_page' => -1,
						'facetwp' => true,
						'tax_query'	=> array(
							array(
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $find_us_term_id,
							),
						),
					);

					$posts = array();

					// Loop through the posts, storing data into the $posts array
					$query = new WP_Query( $args );
					if ( $query->have_posts() ) {

						// echo do_shortcode( '[facetwp facet="contador_tiendas"]' );

						while ( $query->have_posts() ) { $query->the_post();

							// $results_count_class = 'd-none';
							// if( $query->found_posts == 1 ) {
							// 	$results_count_class = '';
							// }

							// $url_google = esc_url( smn_get_store_google_url() );
							// echo '<div class="'. $results_count_class .'">';
							// 	echo wpautop( '<a class="results-map-link" href="'. $url_google .'" target="_blank" rel="noopener noreferrer">'. __( 'Ver en maps', 'smn' ) .'</a>' );
							// echo '</div>';

							$terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );							
							$ancestors = get_ancestors( $terms[0], 'zona' );
							if ( count($ancestors) > 1 ) {
								$second_level_term_id = array_slice( $ancestors, -2, 1 )[0];
								$posts[ $second_level_term_id ][] = $post;
							} else {
								$posts[ $terms[0] ][] = $post;
							}
						}

					}

					wp_reset_query();

					$zonas_ids_ordenadas = get_terms( array(
						'taxonomy' => $taxonomy,
						'hide_empty' => false,
						'orderby' => 'term_order',
						'order' => 'ASC',
						'parent' => $find_us_term_id,
						'fields' => 'ids',
					) );

					$posts_ordenado = array();
					foreach ( $zonas_ids_ordenadas as $zona_id ) {
						if ( array_key_exists( $zona_id, $posts ) ) {
							$posts_ordenado[ $zona_id ] = $posts[ $zona_id ];
						}
					}
					?>

					<div class="facetwp-template">

						<?php foreach ( $posts_ordenado as $tier => $post_arrays ) : 

								$term = get_term( $tier, $taxonomy );
								?>

						<h2><?php echo esc_html( $term->name ); ?></h2>

						<div class="row <?php echo $tier; ?>">

							<?php foreach ( $post_arrays as $post ) : ?>

								<div class="col-lg-6 col-xl-4">

									<?php setup_postdata( $post ); ?>

									<?php get_template_part( 'loop-templates/content', 'tienda' ); ?>

								</div>
							
							<?php endforeach; ?>

						</div>

						<?php endforeach; 

						wp_reset_postdata();
						?>

					</div>

					<script>
						
						(function ($) {
							
							$(document).on('facetwp-loaded', function() {
								
								var defaultText = '<?php echo __('Resultados', 'smn'); ?>';
								var selectedZone = FWP.facets.buscar_provincia_esp;

								if ( typeof(selectedZone) !== 'undefined' && selectedZone.length > 0) {

									var htmlString = FWP.response.facets.buscar_provincia_esp;
									var $html = jQuery(htmlString);
									var selectedZoneLabel = $html.find('option[value=' + selectedZone + ']').text();
									var selectedZoneGoogleMapsQueryString = 'https://www.google.com/maps/search/?api=1&query=Loco+Polo+en+provincia+de+' + selectedZoneLabel;
									$('.results-map-link').removeClass('d-none').attr('href', selectedZoneGoogleMapsQueryString);
									$('.facetwp-type-pager').prev().text( '<?php echo __('Resultados en "%s"', 'smn'); ?>'.replace('%s', selectedZoneLabel));



								} else {
									$('.facetwp-type-pager').prev().text(defaultText);
									$('.results-map-link').addClass('d-none').attr('href', '#');
								}

							});

						 })(jQuery);
						
					</script>


				</main>

			</div><!-- #primary -->

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #<?php echo $wrapper_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ok. ?> -->

<?php
get_footer();
