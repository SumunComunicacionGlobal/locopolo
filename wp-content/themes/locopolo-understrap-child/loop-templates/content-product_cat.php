<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$category = $args['category'];
$lazy = isset($args['lazy']) ? $args['lazy'] : false;

// Obtener el título y la descripción de la categoría
$title = esc_html( $category->name );
$description = $category->description;

$carousel_image_wrapper_position = get_field( 'carousel_image_wrapper_position', $category );
?>

<div class="product-card--wrapper">

    <span class="product-card--title-badge"><span class="badge--wrapper"><span class="badge"><?php echo $title; ?></span></span></span>

    <div class="product-card--image-wrapper animation-floating" style="<?php echo $carousel_image_wrapper_position; ?>">

        <?php smn_multi_image( $category, $lazy ); ?>

    </div>

    <div class="card product-card product-cat-card shadow">

        <a class="btn btn-sm btn-secondary shadow-sm product-card--button" href="<?php echo get_term_link( $category, 'product_cat' ); ?>"><?php echo __( 'Ver más', 'smn' ); ?> <span class="btn-pointer-icon"></span></a>

        <div class="card-body">

            <h2 class="product-card--title"><a class="stretched-link" href="<?php echo get_term_link( $category ); ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a></h2>

            <?php echo wpautop( $description ); ?>

        </div>

        <?php smn_product_subcats_icons( $category ); ?>

    </div>

    <?php smn_new_product_sticker( $category ); ?>

</div>
