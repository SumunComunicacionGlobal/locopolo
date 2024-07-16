<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function year_shortcode () {
    $year = date_i18n ('Y');
    return $year;
}
add_shortcode ('year', 'year_shortcode');

function selector_glovo_shortcode () {

    $glovo_list = get_field( 'glovo_list', 'option' );

    if ( !$glovo_list ) return false;

    $glovo_list = explode( "\n", $glovo_list );
    
    $r = '';


    $r .= '<div class="row">';

        $r .= '<div class="col-md-8 col-xl-10 mb-3">';

            $r .= '<select class="form-select glovo-select" aria-label="'. __( 'Selecciona ciudad', 'smn' ) .'">';

                $r .= '<option value="0" selected>-- '. __( 'Selecciona ciudad', 'smn' ) .' --</option>';

                foreach ( $glovo_list as $item ) {

                    $item = explode( '|', $item );
                    $city = $item[0];
                    $url = $item[1];

                    $r .= '<option value="'. $url .'">'. $city .'</option>';

                }

            $r .= '</select>';

        $r .= '</div>';

        $r .= '<div class="col-md-4 col-xl-2 mb-3">';

            $logo_glovo_url = get_stylesheet_directory_uri() . '/img/logo-glovo.svg';
            $r .= '<a href="#" class="btn btn-link btn-secondary bg-secondary border-secondary d-block glovo-select-button disabled" role="button" target="_blank" rel="noopener noreferrer"><img style="margin-top: -6px;" src="' . $logo_glovo_url . '" alt="'. __( 'Glovo', 'smn' ) .'" /></a>';

        $r .= '</div>';

    $r .= '</div>';

    ob_start();
    ?>

    <script>
        jQuery('.glovo-select').on('change', function() {
            var glovoButtons = jQuery('.glovo-select-button');
            if ( this.value == '0' ) {
                glovoButtons.attr('href', '#' ).addClass('disabled', true);
            } else {
                glovoButtons.attr('href', this.value ).removeClass('disabled');
            }
        });
    </script>

    <?php
    $r .= ob_get_clean();

    return $r;

}
add_shortcode ('selector_glovo', 'selector_glovo_shortcode');


// Add mapa_establecimientos shortcode
add_shortcode( 'mapa_establecimientos', 'mapa_establecimientos_shortcode' );
function mapa_establecimientos_shortcode () {

    $facet = get_field( 'find_us_facet_id' );
    if ( !$facet ) {
        $facet = 'buscar_provincia_esp';
    }

    $r = '';


    $r .= '<div class="zones-map">';

        $r .= '<div class="zones-map--map">';

            $r .= '<div id="zones-map">'. get_svg_map() .'</div>';

        $r .= '</div>';

    $r .= '</div>';

    ob_start();
    ?>

    <script>
        // Javascript to filter facetwp results on data-zone click
        jQuery('.zones-map--zones a, .has-sale-points[data-zone]').on('click', function(e) {
            e.preventDefault();
            var zone = jQuery(this).data('zone');
            FWP.facets['<?php echo $facet; ?>'] = [zone];
            FWP.fetchData();
            // FWP.setHash();
        });
    </script>

    <?php
    $r .= ob_get_clean();
    
    return $r;

}

function get_svg_map() {
    $svg = file_get_contents( get_stylesheet_directory() . '/img/maps/esp.svg' );
    $svg_meta_field = get_field( 'svg_url');

    $find_us_term_id = 11;
    $find_us_term_id_meta_field = get_field( 'find_us_term_id' );
    if ( $find_us_term_id_meta_field ) {
        $find_us_term_id = $find_us_term_id_meta_field;
    }

    if ( $svg_meta_field ) {
        $svg_meta_field = str_replace( get_stylesheet_directory_uri(), get_stylesheet_directory(), $svg_meta_field );
        $svg = file_get_contents( $svg_meta_field );
    }

    $terms = get_terms( array(
        'taxonomy' => 'zona',
        'hide_empty' => false,
        'parent'    => $find_us_term_id,
    ) );

    $svg = str_replace( 
        array(
            ' width="0" height="0"',
            ' cx="',
            ' cy="',
            '<circle ',
            '></circle>',
        ),
        array( 
            '',
            ' x="',
            ' y="',
            '<rect ',
            ' />',
        ), 
        $svg );

    foreach ($terms as $term) {

        $svg = str_replace('data-zone="'.$term->slug.'"', 'title="'.$term->name.'" data-zone="'.$term->slug.'"', $svg);

        if ( $term->count > 0 ) {
            $svg = str_replace('data-zone="'.$term->slug.'" class="', 'data-zone="'.$term->slug.'" class="has-sale-points ', $svg);
        } else {
            $svg = str_replace('data-zone="'.$term->slug.'" class="', ' class="no-sale-points ', $svg);
        }

    }

    // // replace <rect> with class .has-sale-points with map pin image
    $svg = str_replace('<rect ', '<image xlink:href="'.get_stylesheet_directory_uri().'/img/map-pin.svg" ', $svg);


    return $svg;

}