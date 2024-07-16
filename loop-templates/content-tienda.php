<?php
/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$url_google = esc_url( smn_get_store_google_url() );
$city = get_field( 'tienda_ciudad' );
if ( !$city ) {
	$city = get_the_title();
}

$address = get_field( 'tienda_direccion' );
$zip_code = get_field( 'tienda_codigo_postal' );

// $address_string = wpautop( implode( '<br>', array( $address, $zip_code ) ) );
$address_string = wpautop( $address );

$subtitulo_tienda = '';
if ( has_term( TIENDA_OFICIAL_TERM_ID, 'tipo-tienda' ) ) {
	$subtitulo_tienda = '<span class="official-store">' . get_term( TIENDA_OFICIAL_TERM_ID, 'tipo-tienda' )->name . '</span>';
} else {
	$subtitulo_tienda = esc_html( get_the_title() );
}

$subtitulo_tienda = '<p class="tipo-tienda-tag">'. $subtitulo_tienda .'</p>';

$tooltip_content = '';
$tooltip_content .= get_the_term_list( get_the_ID(), 'tipo-tienda', '<h4>', ' · ', '</h4>' );
// strip link tags
$tooltip_content = strip_tags( $tooltip_content, '<h4>' );
$tooltip_content .= $subtitulo_tienda;
$tooltip_content .= wpautop( $address );

if ( $post->post_content ) {
	$tooltip_content .= $post->post_content;
	$tooltip_content .= wpautop( '<a href="' . $url_google . '" target="_blank" rel="noopener noreferrer">' . __( 'Ver Maps', 'smn' ) . '</a>' );
	$tooltip_content = esc_html( $tooltip_content );
}
?>

<article <?php post_class( 'mb-5 card' ); ?> id="post-<?php the_ID(); ?>">

	<div class="card-body">

		<div class="row">

			<div class="col-9">

				<h3 class="entry-title card-title"><?php echo $city; ?></h3>

				<div class="entry-content card-text">

					<?php echo $subtitulo_tienda; ?>

					<?php echo $address_string;	?>

					<a class="btn btn-primary" target="_blank" rel="noopener noreferrer" href="<?php echo $url_google; ?>"><?php echo __( 'Ver Maps', 'smn' ); ?></a>

					<?php // understrap_entry_footer(); ?>

				</div><!-- .entry-content -->

			</div>

			<div class="col-3">

				<?php if ( $tooltip_content ) { 
					$tooltip_content = esc_html( $tooltip_content );
					?>
					<a href="#" data-bs-placement="bottom" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<?php echo $tooltip_content; ?>">
				<?php } else { ?>
					<a target="_blank" rel="noopener noreferrer" href="<?php echo $url_google; ?>">
				<?php } ?>
					<?php smn_store_icon(); ?>
				</a>

			</div>

		</div>

	</div>

</article><!-- #post-<?php the_ID(); ?> -->
