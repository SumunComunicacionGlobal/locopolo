<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
global $post;

$lazy = isset($args['lazy']) ? $args['lazy'] : false;
?>

<div class="product-card--wrapper">

    <div class="product-card--image-wrapper animation-floating">

        <?php smn_multi_image( $post, $lazy ); ?>

    </div>

    <div class="card product-card shadow">

        <a class="btn btn-sm btn-secondary shadow-sm product-card--button" href="<?php echo get_the_permalink(); ?>"><?php echo __( 'Ver más', 'smn' ); ?> <span class="btn-pointer-icon"></span></a>

        <div class="card-body">

            <h2 class="product-card--title">
                <a class="stretched-link" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                    <?php the_title(); ?>
                </a>
            </h2>

            <p><?php the_content(); ?></p>
                
        </div>

        <?php smn_product_card_meta( $post ); ?>

    </div>

    <?php smn_new_product_sticker( $post ); ?>

</div>
        

