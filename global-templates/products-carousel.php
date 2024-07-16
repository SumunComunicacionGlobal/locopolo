<?php
/**
 * Carrusel de Categorías de Productos
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$category = null;

$q_args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
);


if ( isset( $args['category'] ) ) {
    $category = $args['category'];
} elseif ( is_tax( 'product_cat') ) {
    $category = get_queried_object();
}

if ( $category ) {
    $q_args['tax_query'] = array( array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => $category->term_id,
                            ),
    );
}

$q = new WP_Query( $q_args );

if ( $q->have_posts() ) {

    $additional_classes = '';
    if ( $q->post_count == 1 ) $additional_classes = 'one-slide-centered-carousel';

    $sticker = '';
    if ( $category ) $sticker = get_field( 'term_icon', $category);
    if ( $sticker ) {
        $sticker = '<span class="sticker">' . wp_get_attachment_image( $sticker, 'thumbnail' ) . '</span>';
    }

    echo '<div class="container">';

        echo '<span class="badge-sticker--wrapper">'. $sticker .'<span class="badge--wrapper term-badge"><h2 class="badge">' . $category->name . '</h2></span></span>';

    echo '</div>';
    
    echo '<div class="products-carousel alignfullxxx'. $additional_classes .'">';

        echo '<div class="slick-carousel">';

        while ( $q->have_posts() ) {

            $q->the_post();

            echo '<div class="slide">';

                echo '<div class="container">';

                    get_template_part( 'loop-templates/content', 'product', array( 'lazy' => false ) );

                echo '</div>';

            echo '</div>';
        }

        echo '</div>';

        echo '<div class="slick-navigation-container container d-flex"></div>';

        echo '<div class="slick-invisible-arrow slick-invisible-prev"></div>';
        echo '<div class="slick-invisible-arrow slick-invisible-next"></div>';

    echo '</div>';
}

wp_reset_postdata();
?>
