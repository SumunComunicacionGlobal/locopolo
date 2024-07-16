<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function smn_valores_nutricionales() {


    $field_group_key = 40;
    $fields = acf_get_fields($field_group_key);
    
    $grasas_fields = array_filter($fields, function($field_obj) {
        return isset($field_obj['name']) && 
        $field_obj['name'] === 'valores_grasas_saturadas' ||
        $field_obj['name'] === 'valores_grasas_monoinsaturadas' ||
        $field_obj['name'] === 'valores_grasas_poliinsaturadas';
    });    

    $azucares_fields = array_filter($fields, function($field_obj) {
        return isset($field_obj['name']) && 
        $field_obj['name'] === 'valores_azucares';
    });

    $fields = array_diff_key($fields, $grasas_fields, $azucares_fields);

    $fields_values = get_fields();
    $first_column_count = ceil( count($fields)/2 );

    if ( $fields ) { ?>

        <div class="valores-nutricionales mb-5">

            <div class="row">

                <?php foreach( $fields as $index => $field_obj ) { 
                    if ( !isset( $fields_values[$field_obj['name']] ) ) continue;
                    if ( !$fields_values[$field_obj['name']] ) continue;

                    $value = $fields_values[$field_obj['name']];

                    if ( 'valores_kcal' == $field_obj['name'] ) {
                        $value = ceil( $value );
                    } else {
                        $decimals = 1;
                        if ( $value < 1 ) $decimals = 2;
                        $value = round( $value, $decimals );
                    }

                    $value_formatted = implode( '&nbsp;', array( $field_obj['prepend'], $value, $field_obj['append'] ) );
                    $tooltip_content = $value_formatted;
                    $min_value = 0;
                    $max_value = 50;
                    if ( $field_obj['min'] ) $min_value = $field_obj['min'];
                    if ( $field_obj['max'] ) $max_value = $field_obj['max'];
                    $rango_text = sprintf( __( '(de %s a %s%s)', 'smn'), $min_value, $max_value, $field_obj['append'] );
                    $percentage = (float) $value / $max_value * 100;

                    if ( 'valores_grasas' == $field_obj['name'] ) {
                        $tooltip_content = $field_obj['label'] . ': ' . $value_formatted . '</br>';
                        foreach( $grasas_fields as $grasa_field ) {
                            $subvalue = $fields_values[$grasa_field['name']];
                            if ( !$subvalue ) continue;
                            $decimals = 1;
                            if ( $subvalue < 1 ) $decimals = 2;
                            $subvalue = round( $value, $decimals );
                            $tooltip_content .= $grasa_field['label'] . ': ' . $subvalue . '&nbsp;' . $grasa_field['append'] . '<br>';
                        }
                    } elseif ( 'valores_hidratos_de_carbono' == $field_obj['name'] ) {
                        $tooltip_content = $field_obj['label'] . ': ' . $value_formatted . '</br>';
                        foreach( $azucares_fields as $azucar_field ) {
                            $subvalue = $fields_values[$azucar_field['name']];
                            if ( !$subvalue ) continue;
                            $decimals = 1;
                            if ( $subvalue < 1 ) $decimals = 2;
                            $subvalue = round( $value, $decimals );
                            $subvalue = round( $subvalue, 1 );
                            $tooltip_content .= $azucar_field['label'] . ': ' . $subvalue . '&nbsp;' . $azucar_field['append'] . '<br>';
                        }
                    }

                    $tooltip_content = esc_html( wpautop( $tooltip_content ) );
                    ?>

                    <div class="col-lg-6">

                        <a href="#" class="valores-nutricionales--item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?php echo $tooltip_content; ?>" data-bs-html="true">

                            <div class="valores-nutricionales--item-title"><?php echo $field_obj['label']; ?> <?php echo $rango_text; ?></div>

                            <div class="progress--wrapper">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-label="<?php echo $field_obj['label']; ?>" aria-valuenow="<?php echo $value; ?>" aria-valuemin="<?php echo $min_value; ?>" aria-valuemax="<?php echo $max_value; ?>"></div>
                                </div>
                            </div>

                        </a>

                    </div>

                <?php } ?>

            </div>

        </div>

    <?php } ?>

    <p class="small"><?php echo __( 'Alérgenos', 'smn' ); ?></p>

    <?php
        $ingredients = get_field( 'ingredients' );
        if ( $ingredients ) {
            echo '<div class="small text-muted">'. wpautop( $ingredients ) .'</div>';
        }
    ?>

    <div class="row align-items-center mb-4">

        <div class="col-auto me-auto">

            <?php smn_alergenos(); ?>

        </div>

    <?php
    add_filter( 'acf/settings/current_language',  '__return_false' );
    $pdf_id = get_field('pdf_valores_alergenos', 'option');
    // add_filter( 'acf/settings/current_language',  '__return_true' );

    if ( $pdf_id) { ?>

        <div class="col-auto valores-nutricionales--ver-todo">
            <a class="btn btn-link px-0" href="<?php echo wp_get_attachment_url( $pdf_id ); ?>" target="_blank" rel="noopener noreferrer"><?php echo __( 'Ver todos', 'smn' ); ?></a>
        </div>

    <?php } ?>

    </div>

<?php }

function smn_alergenos() {


    $terms = get_the_terms( null, 'allergen' );

    if ( $terms ) {

        echo '<div class="term-icons allergens">';

            foreach( $terms as $term ) {
                echo smn_get_product_cat_icon( $term );
            }

        echo '</div>';
    }


}

function smn_product_gallery() {
    
    $video_id = get_field( 'product_video' );

    if( $video_id ) {

        $video_url = wp_get_attachment_url( $video_id );
        echo '<div class="product-video">';
            echo wp_video_shortcode( array( 'src' => $video_url ) );
        echo '</div>';
    }
    
}

function smn_related_products() {
    
    // Get products with same product_cat
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post__not_in' => array( get_the_ID() ),
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => wp_get_post_terms( get_the_ID(), 'product_cat', array( 'fields' => 'slugs' ) ),
            ),
        ),
    );

    $q = new WP_Query( $args );
    // query loop
    if ( $q->have_posts() ) {
        echo '<div class="related-products-carousel">';

            while ( $q->have_posts() ) { $q->the_post();

                get_template_part( 'loop-templates/content-product', 'related' );

            }

        echo '</div>';
    }

    wp_reset_postdata();

}

function smn_get_store_google_url() {

    $google_url = get_field( 'tienda_url_google' );
    $query_array = array();

    if ( !$google_url ) {

        $address = get_field( 'tienda_direccion' );
        $city = get_field( 'tienda_ciudad' );

        if ( $address ) $query_array[] = $address;
        if ( $city ) $query_array[] = $city;

        $query_string = implode( '+', $query_array );
        // encode string for url
        $query_string = urlencode( $query_string );
    
        $google_url = 'https://www.google.es/maps/search/';
        // $google_url .= 'loco+polo+';
        $google_url .= str_replace(' ', '+', get_the_title() ) . '+';
        $google_url .= str_replace( ' ', '+', $query_string );
    }

    return $google_url;
}

function smn_store_icon() {

    $img_url = get_stylesheet_directory_uri() . '/img/store-icon-circle.svg';
    echo '<img class="store-icon-circle" src="' . $img_url . '" alt="' . __( 'Icono de una tienda', 'smn' ) . '" />';

}

function smn_get_product_cat_thumbnail_id( $category = null ) {

    if ( !$category ) {
        $category = get_queried_object();
    }

    $image_id = get_term_meta($category->term_id, 'thumbnail_id', true);

    // if ( !$image_id ) {
    //     // Obtener el id de la imagen destacada del primer producto de la categoría
    //     $args = array(
    //         'post_type' => 'product',
    //         'posts_per_page' => 1,
    //         'product_cat' => $category->slug,
    //     );
    //     $products = new WP_Query( $args );
    //     if ( $products->have_posts() ) {
    //         while ( $products->have_posts() ) {
    //             $products->the_post();
    //             $image_id = get_post_thumbnail_id();
    //         }
    //     }
    //     wp_reset_postdata();
    // }

    return $image_id;

}

function smn_multi_image( $object, $lazy = false ) {

    $max_additional_images = 2;
    $link = false;

    if ( is_a( $object, 'WP_Post' ) ) {

        $main_image_id = get_post_thumbnail_id( $object );
        $additional_images_ids = get_post_meta( $object->ID, 'product_image_gallery', true );
        if ( $additional_images_ids && count( $additional_images_ids ) > $max_additional_images ) {
            $additional_images_ids = array_slice( $additional_images_ids, 0, $max_additional_images );
        }
        $title = $object->post_title;
        $link = get_the_permalink( $object );
        $main_term = wp_get_post_terms( $object->ID, 'product_cat' )[0];
        $spark_position = get_field( 'spark_position', $main_term );

    } elseif ( is_a( $object, 'WP_Term' ) ) {

        $main_image_id = smn_get_product_cat_thumbnail_id( $object );
        $additional_images_ids = get_term_meta( $object->term_id, 'product_image_gallery', true );
        $link = get_term_link( $object );
        $spark_position = get_field( 'spark_position', $object );

        if ( $additional_images_ids && count( $additional_images_ids ) > $max_additional_images ) {
            $additional_images_ids = array_slice( $additional_images_ids, 0, $max_additional_images );
        }

        if ( !$main_image_id && $additional_images_ids ) {
            $main_image_id = $additional_images_ids[0];
        }

        $title = $object->name;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $max_additional_images,
            'product_cat' => $object->slug,
        );
        
        // $additional_images_ids = array();
        // $products = new WP_Query( $args );
        
        // if ( $products->have_posts() ) {

        //     while ( $products->have_posts() ) { $products->the_post();
        //         $additional_images_ids[] = get_post_thumbnail_id();
        //     }

        // }

        wp_reset_postdata();

    } else {
        return;
    }

    if ( is_singular( 'product' ) ) $link = false;


    if ( $main_image_id ) {
        echo '<div class="multi-featured-image">';

            if ( $link ) {
                echo '<a href="'. $link .'">';
            }

            echo '<div class="multi-featured-image--inner">';

                if ( $lazy ) {

                    echo '<img data-lazy="'. wp_get_attachment_image_url( $main_image_id, 'medium_large' ) .'" alt="'. $title .'" />';

                } else {
                    
                    echo wp_get_attachment_image( $main_image_id, 'medium_large' );

                }

                if ( $additional_images_ids ) {

                        // echo '<div class="multi-featured-image--thumbnails">';

                            foreach ( $additional_images_ids as $image_id ) {

                                if ( $lazy ) {

                                    echo '<img data-lazy="'. wp_get_attachment_image_url( $image_id, 'medium_large' ) .'" alt="'. $title .'" />';
                            
                                } else {

                                    echo wp_get_attachment_image( $image_id, 'medium_large' );

                                }
                            }

                        // echo '</div>';

                }

                echo '<span class="image-spark" style="'. $spark_position .'"></span>';

            echo '</div>';

            if ( $link ) {
                echo '</a>';
            }

        echo '</div>';
    }


}

function smn_new_product_sticker( $object ) {

    $is_new = get_field( 'new', $object );
    if ( $is_new ) {
        echo '<div class="new-product-sticker"><img src="'. get_stylesheet_directory_uri().'/img/sticker-new.svg" alt="'. __( 'Nuevo', 'smn' ) . '" /></div>';
    }

}

function smn_brand_logos_dropdown_old() {

    // Get product_cats with brand_icon term meta
    $args = array(
        'taxonomy' => 'product_cat',
        'meta_query' => array(
            array(
                'key' => 'brand_icon',
                'value' => '',
                'compare' => '!=',
            ),
        ),
    );

    $product_cats = get_terms( $args );

    $custom_logo_id = get_theme_mod( 'custom_logo' );

    echo '<div class="dropdown brand-logos-dropdown">';

        echo '<a href="#" class="dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
            echo wp_get_attachment_image( $custom_logo_id, 'medium' );
        echo '</a>';

        if ( $product_cats ) {

            echo '<div class="dropdown-menu dropdown-transparent">';

                foreach ( $product_cats as $product_cat ) {

                    $brand_icon_id = get_term_meta( $product_cat->term_id, 'brand_icon', true );
                    $stamp_icon_id = get_term_meta( $product_cat->term_id, 'stamp_icon', true );
                    $brand_icon_image = wp_get_attachment_image( $brand_icon_id, 'medium' );
                    $term_slug = sanitize_title( $product_cat->name );

                    echo '<div class="brand-logos-dropdown--item '. $term_slug .'">';

                        // if ( $product_cat->term_id == HOME_TERM_ID ) {
                        //     echo '<a rel="home" href="'. get_home_url() .'">';
                        // } else {
                            echo '<a href="'. get_term_link( $product_cat ) .'">';
                        // }
                            echo '<span class="brand-logos-dropdown--logo">' . $brand_icon_image . '</span>';
                            if ( $stamp_icon_id ) {
                                $sticker_icon_image = wp_get_attachment_image( $stamp_icon_id, 'medium' );
                                echo '<span class="brand-logos-dropdown--sticker">' . $sticker_icon_image . '</span>';
                            }
                        echo '</a>';

                        smn_new_product_sticker( $product_cat );

                    echo '</div>';

                }

            echo '</div>';

        }

    echo '</div>';

}

function smn_brand_logos_dropdown() {

    add_filter( 'acf/settings/current_language',  '__return_false' );

    $custom_logo_id = get_theme_mod( 'custom_logo' );

    echo '<div class="dropdown brand-logos-dropdown">';

        echo '<a href="#" class="dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
            echo wp_get_attachment_image( $custom_logo_id, 'medium' );
        echo '</a>';

        if( have_rows( 'brand_dropdown', 'option' ) ) {

            echo '<div class="dropdown-menu dropdown-transparent">';

                while ( have_rows( 'brand_dropdown', 'option' ) ) { the_row();

                    $brand_icon_id = get_sub_field( 'brand_dropdown_img', 'option' );
                    $brand_term_id = get_sub_field( 'brand_dropdown_term_id', 'option' );
                    $stamp_icon_id = get_sub_field( 'brand_dropdown_stamp', 'option' );
                    $sticker_id = get_sub_field( 'brand_dropdown_sticker', 'option' );

                    if ( $brand_term_id ) {
                        $brand_link = get_term_link( $brand_term_id );
                        $term_slug = sanitize_title( get_term( $brand_term_id )->name );
                    } else {
                        $brand_link = get_home_url();
                        $term_slug = sanitize_title( get_bloginfo( 'name' ) );
                    }

                    $brand_icon_image = wp_get_attachment_image( $brand_icon_id, 'medium' );

                    echo '<div class="brand-logos-dropdown--item '. $term_slug .'">';

                        // if ( $product_cat->term_id == HOME_TERM_ID ) {
                        //     echo '<a rel="home" href="'. get_home_url() .'">';
                        // } else {
                            echo '<a href="'. $brand_link .'">';
                        // }
                            echo '<span class="brand-logos-dropdown--logo">' . $brand_icon_image . '</span>';
                            if ( $stamp_icon_id ) {
                                $sticker_icon_image = wp_get_attachment_image( $stamp_icon_id, 'medium' );
                                echo '<span class="brand-logos-dropdown--sticker">' . $sticker_icon_image . '</span>';
                            }
                        echo '</a>';

                        if ( $sticker_id ) {
                            echo '<div class="new-product-sticker">' . wp_get_attachment_image( $sticker_id, 'medium' ) . '</div>';
                        }


                    echo '</div>';

                }

            echo '</div>';

        }

    echo '</div>';

    // add_filter( 'acf/settings/current_language',  '__return_true' );

}

function smn_brand_stamps_dropup() {

    // Get product_cats with brand_icon term meta
    $args = array(
        'taxonomy' => 'product_cat',
        'meta_query' => array(
            array(
                'key' => 'stamp_icon',
                'value' => '',
                'compare' => '!=',
            ),
        ),
    );

    $product_cats = get_terms( $args );

    if ( $product_cats ) {

                foreach ( $product_cats as $index => $product_cat ) {

                    // if ( $product_cat->term_id == HOME_TERM_ID ) {
                    //     $link = get_home_url();
                    // } else {
                        $link = get_term_link( $product_cat );
                    // }

                    $stamp_icon_id = get_term_meta( $product_cat->term_id, 'stamp_icon', true );
                    $term_slug = sanitize_title( $product_cat->name );

                    if ( $index == 0 ) {

                        echo '<div class="dropup brand-stamps-dropup d-none d-md-block">';

                            echo '<a href="'. $link .'" class="dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                                echo wp_get_attachment_image( $stamp_icon_id, 'medium' );
                            echo '</a>';
                        
                            echo '<div class="dropdown-menu dropdown-transparent">';
            
                    } else {

                        echo '<div class="brand-stamps-dropup--item '. $term_slug .'">';

                            echo '<a href="'. $link .'">';

                                echo wp_get_attachment_image( $stamp_icon_id, 'medium' );

                            echo '</a>';

                        echo '</div>';

                    }

                }

            echo '</div>';

        echo '</div>';

    }

}

function smn_product_subcats_icons( $category ) {

    $args = array(
        'taxonomy' => 'product_cat',
        'parent' => $category->term_id,
    );

    $subcats = get_terms( $args );

    if ( $subcats ) {

        echo '<div class="product-card--subterms-icons d-none d-lg-flex">';

            foreach ( $subcats as $subcat ) {

                echo '<div class="term--wrapper">';
                    $icon_image = smn_get_product_cat_icon( $subcat );
                    echo $icon_image;
                    echo '<span class="term-name btn btn-secondary shadow-sm">'. $subcat->name .'</span>';
                echo '</div>';

            }

        echo '</div>';

    }

}

function smn_get_product_cat_icon( $category ) {

    $icon_id = get_term_meta( $category->term_id, 'term_icon', true );
    $icon_image = wp_get_attachment_image( $icon_id, 'medium' );
    $class = 'term-icon';

    if ( $icon_image ) {
        if ( 'allergen' == $category->taxonomy ) {
            return '<span class="'. $class .'">' . $icon_image . '</span>';
        } else {
            return '<a href="'. get_term_link( $category ) .'" class="'. $class .'">' . $icon_image . '</a>';
        }
    }

    return false;

}

function smn_product_card_meta() {

    $kcal = get_field( 'valores_kcal' );
    $kcal = ceil( $kcal );

    echo '<div class="product-card--meta">';

        echo '<div class="product-card--meta-item allergens">';
            smn_alergenos();
        echo '</div>';

        if ( $kcal ) {
            $field_obj = get_field_object( 'valores_kcal' );
            if ( $field_obj['append'] ) {
                $kcal .= '&nbsp;' . $field_obj['append'];
            }
            echo '<span class="product-card--meta-item kcal">' . $kcal . '</span>';
        }

    echo '</div>';

}

function smn_find_us_navigation() {

    // get pages with find-us template
    $args = array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-templates/find-us.php',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );

    $current_page_id = get_the_ID();

    // query loop
    $q = new WP_Query( $args );

    if ( $q->have_posts() ) {

        // get current page id position in the loop
        $current_page_position = 0;
        $i = 0;
        while ( $q->have_posts() ) { $q->the_post();
            if ( get_the_ID() == $current_page_id ) {
                $current_page_position = $i;
            }
            $i++;
        }

        $prev_page_index = ( isset( $q->posts[$current_page_position - 1] ) ) ? $current_page_position - 1 : count($q->posts) - 1;
        $next_page_index = (isset($q->posts[$current_page_position + 1])) ? $current_page_position + 1 : 0;


        echo '<nav class="find-us-navigation">';

            echo '<ul class="nav justify-content-center align-items-center">';

                echo '<li class="nav-item">';

                    echo '<a class="nav-link nav-link-prev" href="'. get_the_permalink( $q->posts[$prev_page_index]->ID ) .'"></a>';

                echo '</li>';

                while ( $q->have_posts() ) { $q->the_post();

                    $term_id = get_field( 'find_us_term_id' );
                    $term = get_term( $term_id );

                    // get term's post count
                    $args_tiendas = array(
                        'post_type' => 'tienda',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'zona',
                                'terms' => array( $term_id ),
                                'include_children' => true,
                            ),
                        ),
                    );

                    $posts_tiendas = get_posts( $args_tiendas );
                    $term_count = count( $posts_tiendas );

                    if ( $term_count ) {

                        $active_class = '';
                        if ( $current_page_id == get_the_ID() ) $active_class = 'active';

                        echo '<li class="nav-item">';

                            echo '<a class="nav-link '. $active_class .' px-1 text-uppercase" href="'. get_the_permalink() .'">'. $term->slug .'('. $term_count .')</a>';

                        echo '</li>';

                    }

                }

                echo '<li class="nav-item">';

                    echo '<a class="nav-link nav-link-next" href="'. get_the_permalink( $q->posts[$next_page_index]->ID ) .'"></a>';

                echo '</li>';

            echo '</ul>';

        echo '</nav>';

    }

    wp_reset_postdata();

}
