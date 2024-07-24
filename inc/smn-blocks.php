<?php 

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


if ( function_exists( 'register_block_style' ) ) {

    register_block_style(
        'core/columns',
        array(
            'name'         => 'gapless',
            'label'        => __( 'Sin espacio', 'smn-admin' ),
            'is_default'   => false,
        )
    );

    register_block_style(
        'core/list',
        array(
            'name'         => 'list-unstyled',
            'label'        => __( 'Sin viñetas', 'smn-admin' ),
            'is_default'   => false,
        )
    );

    register_block_style(
        'core/group',
        array(
            'name'         => 'faq',
            'label'        => __( 'FAQ', 'smn-admin' ),
            'is_default'   => false,
        )
    );

    register_block_style(
        'core/group',
        array(
            'name'         => 'eye-tooltip',
            'label'        => __( 'Tooltip', 'smn-admin' ),
            'is_default'   => false,
        )
    );


    register_block_style(
        'core/gallery',
        array(
            'name'         => 'slider-gallery',
            'label'        => __( 'Slider', 'smn-admin' ),
            'is_default'   => false,
        )
    );

    $display_text_block_types = array(
        'core/paragraph',
        'core/heading',
    );

    foreach( $display_text_block_types as $block_type ) {

        for ($i=1; $i <= 6; $i++) { 

            register_block_style(
                $block_type,
                array(
                    'name'         => 'h' . $i,
                    'label'        => sprintf( __( 'Imita un h%s', 'smn-admin' ), $i ),
                    'is_default'   => false,
                )
            );

        }

        register_block_style(
            $block_type,
            array(
                'name'         => 'badge',
                'label'        => sprintf( __( 'Biselado', 'smn-admin' ), $i ),
                'is_default'   => false,
            )
        );
            
    }

}

add_filter( 'render_block', 'remove_is_style_prefix', 10, 2 );
function remove_is_style_prefix( $block_content, $block ) {

    if ( isset( $block['attrs']['className'] ) ) {

        if ( str_contains( $block['attrs']['className'], 'is-style-badge' ) ) {
            $block_content = '<span class="badge--wrapper">' . $block_content . '</span>';
        }
    
        $components = array(
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'display-1',
            'display-2',
            'display-3',
            'display-4',
            'lead',
            'list-unstyled',
            'badge',
        );

        $prefixed_components = array();
    
        foreach ( $components as $component ) {
            $prefixed_components[] = 'is-style-' . $component;
        }

        $block_content = str_replace(
            $prefixed_components,
            $components,
            $block_content
        );

    }
    
    return $block_content;
}


add_filter( 'render_block', 'list_block_wrapper', 10, 2 );
function list_block_wrapper( $block_content, $block ) {
    if ( $block['blockName'] === 'core/list' ) {
        $block_content = str_replace( 
            array( '<ul class="', '<ol class="'), 
            array( '<ul class="wp-block-list ', '<ol class="wp-block-list '), $block_content );
        
        $block_content = str_replace( 
            array( '<ul>', '<ol>'), 
            array( '<ul class="wp-block-list">', '<ol class="wp-block-list">'), $block_content );
    }
    return $block_content;
}

add_filter( 'render_block', 'faq_block_modifyer', 10, 2 );
function faq_block_modifyer( $block_content, $block ) {

    if ( isset( $block['attrs']['className'] ) && str_contains( $block['attrs']['className'], 'is-style-faq' ) ) {

        $title = $block['innerBlocks'][0]['innerHTML'];
        $content = str_replace( array($title, 'is-style-faq'), '', $block_content );        

        $block_content = '<div class="wp-block-group is-style-faq accordion">';
            $block_content .= '<div class="accordion-item">';
                $block_content .= '<h2 class="accordion-header faq-title">';
                    $block_content .= '<button type="button" class="accordion-button collapsed" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-html="true" data-bs-trigger="focus" data-bs-title="' . esc_html( $content ) . '">';
                        $block_content .= $title;
                    $block_content .= '</button>';
                $block_content .= '</h2>';
            $block_content .= '</div>';
            // $block_content .= '<div class="faq-content">' . $content . '</div>';
        $block_content .= '</div>';

    }

        
    return $block_content;
}

// Usar el filtro render_block para actualizar el HTML de un bloque si tiene una clase específica
add_filter( 'render_block', 'smn_slider_gallery', 10, 2 );
function smn_slider_gallery( $block_content, $block ) {

    if ( isset( $block['attrs']['className'] ) ) {

        if ( str_contains( $block['attrs']['className'], 'is-style-slider-gallery' ) ) {

            // obtener las ids de las imágenes contenidas en el block gallery
            $img_ids = [];
            foreach ( $block['innerBlocks'] as $innerBlock ) {
                $img_ids[] = $innerBlock['attrs']['id'];
            }

            if ( $img_ids ) {

                $r = '<div class="wp-block-slider-gallery position-relative">';

                    $r .= '<div class="slick-gallery-slider">';

                        foreach ( $img_ids as $img_id ) {

                            // get translated id
                            $img_id = apply_filters( 'wpml_object_id', $img_id, 'attachment' );

                            // Obetener el título de la imagen
                            $img_title = get_the_title( $img_id );

                            $r .= '<div class="slick-gallery-slide">';

                                if ( $img_title ) {

                                    $r .= '<span class="badge-sticker--wrapper">
                                                <span class="sticker sticker-smiley"></span>
                                                <span class="badge--wrapper term-badge">
                                                    <h1 class="badge">
                                                        '. $img_title .'
                                                    </h1>
                                                </span>
                                            </span>';
                                }

                                $r .= wp_get_attachment_image( $img_id, 'large' );

                            $r .= '</div>';

                        }

                    $r .= '</div>';

                    $r .= '<div class="slick-navigation-container d-flex"></div>';

                $r .= '</div>';

                $block_content = $r;
            }

        }

    }

    return $block_content;
}

add_filter( 'render_block', 'smn_eye_tooltip', 10, 2 );
function smn_eye_tooltip( $block_content, $block ) {

    if ( isset( $block['attrs']['className'] ) && str_contains( $block['attrs']['className'], 'is-style-eye-tooltip' ) ) {

        $title = $block['innerBlocks'][0]['innerHTML'];
        $content = str_replace( $title, '', $block_content );      

        $content = str_replace( '<figure', '<div', $content );
        $content = str_replace( '</figure>', '</div>', $content );

        $title .= '<button type="button" class="btn btn-white btn-sm btn-eye shadow-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="' . esc_attr( $content ) . '">';
            $title .= '<img src="'. get_stylesheet_directory_uri() . '/img/eye.svg" alt="Ver más" class="eye-icon" width="24" height="24">';
        $title .= '</button>';
        $title = '<div class="eye-tooltip--wrapper">' . $title . '</div>';

        
        $block_content = $title;
        
    }

        
    return $block_content;
}