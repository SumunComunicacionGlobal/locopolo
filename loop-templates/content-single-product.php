<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$slides_to_show = 3;

$kcal = get_field( 'valores_kcal' );
if ( $kcal ) {
	$kcal = ceil( $kcal );
	$field_obj = get_field_object( 'valores_kcal' );
	if ( $field_obj['append'] ) {
		$kcal .= '&nbsp;' . $field_obj['append'];
	}
	$kcal = '<span class="entry-title--additional-info">'. $kcal . '</span>';
}
$product_cats = wp_get_object_terms( get_the_ID(), 'product_cat' );
$product_cats_list = '';
foreach( $product_cats as $cat ) {
	$sticker_image = '';
	$term_icon = get_field( 'term_icon', $cat );
	if ( $term_icon ) {
		$sticker_image = '<span class="sticker">' . wp_get_attachment_image( $term_icon, 'thumbnail' ) . '</span>';
	}
	$product_cats_list = '<div class="position-relative">'.$sticker_image.'<span class="badge--wrapper"><span class="badge">' . $cat->name . '</span></span></div>';
	// $product_cats_list = '<span class="badge">' . $sticker_image . $cat->name . '</span></span>';
}

if ( $product_cats_list ) $product_cats_list = '<div class="term-badges">' . $product_cats_list . '</div>';
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<div class="row">

		<div class="col-md-6 col-lg-7 single-product--image-column">

			<div class="single-product-categories mb-5">

				<?php 
				// echo strip_tags( get_the_term_list( null, 'product_cat', '<div class="term-badges"><span class="badge--wrapper"><span class="badge">' . $sticker_image, '</span></span><span class="badge--wrapper"><span class="badge">', '</span></span></div>' ), array( '<div>', '<span>' ) );
				echo $product_cats_list;
				?>

				<a class="btn btn-dark" href="<?php echo get_permalink( PIDE_YA_ID ); ?>"><?php echo get_the_title( PIDE_YA_ID ); ?></a>

			</div>

			<div class="product-image animation-floating">

				<?php smn_multi_image( $post ); ?>

				<?php // echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

			</div>

		</div>

		<div class="col-md-6 col-lg-5 single-product--content-column">

			<header class="entry-header">

				<?php the_title( '<h1 class="entry-title">', $kcal .'</h1>' ); ?>

			</header><!-- .entry-header -->

			<div class="entry-content">

				<?php
				the_content();
				understrap_link_pages();
				?>

				<?php smn_valores_nutricionales(); ?>

				<div class="row mt-5">

					<?php
					$related_products_col_classes = 'col';
					ob_start();
					smn_product_gallery(); 
					$product_gallery = ob_get_clean();
					if ( $product_gallery ) {
						$slides_to_show = 2;
						$related_products_col_classes = 'col-xl-8';
						
						echo '<div class="col-xl-4 mb-5">';
							echo $product_gallery;
						echo '</div>';
					}
					
					?>



					<div class="<?php echo $related_products_col_classes; ?> mb-5">

						<?php smn_related_products(); ?>
					
					</div>

				</div>

			</div><!-- .entry-content -->

			<footer class="entry-footer">

				<?php understrap_entry_footer(); ?>

			</footer><!-- .entry-footer -->

		</div>

	</div>

</article><!-- #post-<?php the_ID(); ?> -->

<script>

jQuery('.related-products-carousel').slick({
    dots: false,
    arrows: true,
    infinite: false,
    speed: 300,
    slidesToShow: <?php echo $slides_to_show; ?>,
    slidesToScroll: 1,
    autoplay: true,
    centerMode: false,
    // appendArrows: $(this).parent().find('.slick-navigation-container'),
    // appendDots: $(this).parent().find('.slick-navigation-container'),
    responsive: [
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        }
      }
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ]
  });

</script>