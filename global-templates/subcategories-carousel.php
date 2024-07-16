<?php
/**
 * Carrusel de Categorías de Productos
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Obtener la categoría actual
$current_category = null;
if ( is_tax() ) {
    $current_category = get_queried_object();
    $title = $current_category->name;
} else {
    $title = get_post_type_object( 'product' )->labels->name;
}

// Obtener las categorías de nivel 0
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'parent' => 0,
));

// Si estamos en una página de listado, obtener las subcategorías de la categoría actual
if ($current_category) {
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'parent' => $current_category->term_id,
    ));
}

if (!empty($categories)) {

    echo '<div class="container">';

        echo '<span class="badge-sticker--wrapper"><span class="sticker sticker-smiley"></span><span class="badge--wrapper term-badge"><h1 class="badge">' . $title . '</h1></span></span>';

    echo '</div>';
    
    // echo '<div class="alignfullxxx">';

        echo '<div class="products-carousel">';

            echo '<div class="slick-carousel">';

            foreach ($categories as $category) {

                echo '<div class="slide">';

                    echo '<div class="container">';

                        get_template_part( 'loop-templates/content', 'product_cat', array( 'category' => $category, 'lazy' => false ) );

                    echo '</div>';

                echo '</div>';
            }

            echo '</div>';

            echo '<div class="slick-navigation-container container d-flex"></div>';

            echo '<div class="slick-invisible-arrow slick-invisible-prev"></div>';
            echo '<div class="slick-invisible-arrow slick-invisible-next"></div>';

        echo '</div>';

    // echo '</div>';
    
}
?>
