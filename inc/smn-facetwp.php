<?php 

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );

function fwp_add_facet_labels() {
    ?>
      <script>
        (function($) {
          $(document).on('facetwp-loaded', function() {
            $('.facetwp-facet').each(function() {
              var facet = $(this);
              var facet_name = facet.attr('data-name');
              var facet_type = facet.attr('data-type');
              var facet_label = FWP.settings.labels[facet_name];
              if (/*facet_type !== 'pager' && */facet_type !== 'sort' && facet_type !== 'reset' ) {
                if (facet.closest('.facet-wrap').length < 1 && facet.closest('.facetwp-flyout').length < 1) {
                  facet.wrap('<div class="facet-wrap"></div>');
                  facet.before('<p class="facet-label">' + facet_label + '</p>');
                }
              }
            });
          });
        })(jQuery);
      </script>
    <?php
  }
   
  add_action( 'wp_head', 'fwp_add_facet_labels', 100 );

  function fwp_import_posts( $import_id ) {
    if ( function_exists( 'FWP' ) ) {
      FWP()->indexer->index();
    }
  }
  add_action( 'pmxi_after_xml_import', 'fwp_import_posts' );

  add_action( 'wp_footer', function() {
    ?>
    <script>
    document.addEventListener('facetwp-refresh', function() {
        if (FWP.loaded) {
            FWP.setHash();
            window.location.reload();
        }
    });
    </script>
    <?php
    }, 100 );


add_filter( 'facetwp_i18n', function( $string ) {
  if ( isset( FWP()->facet->http_params['lang'] ) ) {
    $lang = FWP()->facet->http_params['lang'];
  
    $translations = [];

    $translations['eu']['Buscar por provincia'] = 'Bilatu probintziaren arabera';
    $translations['en']['Buscar por provincia'] = 'Search by province';
    $translations['fr']['Buscar por provincia'] = 'Recherche par province';
    
    $translations['eu']['Buscar por región'] = 'Bilatu eskualdeka';
    $translations['en']['Buscar por región'] = 'Search by region';
    $translations['fr']['Buscar por región'] = 'Recherche par région';
    
    $translations['eu']['Buscar por zona'] = 'Bilatu eremuaren arabera';
    $translations['en']['Buscar por zona'] = 'Search by zone';
    $translations['fr']['Buscar por zona'] = 'Recherche par quartier';
    
    $translations['eu']['Buscar por distrito'] = 'Bilatu auzoka';
    $translations['en']['Buscar por distrito'] = 'Search by district';
    $translations['fr']['Buscar por distrito'] = 'Recherche par zone';
    
    $translations['eu']['Todas'] = 'Denak';
    $translations['en']['Todas'] = 'Any';
    $translations['fr']['Todas'] = 'Toutes';
    
    $translations['eu']['[total] Puntos de venta'] = '[total] Salmenta puntuak';
    $translations['en']['[total] Puntos de venta'] = '[total] Sales points';
    $translations['fr']['[total] Puntos de venta'] = '[total] Points de vente';
  
    $translations['eu']['1 Punto de venta'] = '1 Salmenta puntua';
    $translations['en']['1 Punto de venta'] = '1 sales point';
    $translations['fr']['1 Punto de venta'] = '1 Point de vente';
  
    $translations['eu']['No hay puntos de venta'] = 'Ez dago salmenta punturik';
    $translations['en']['No hay puntos de venta'] = 'No sales points found';
    $translations['fr']['No hay puntos de venta'] = "Il n'y a pas de points de vente";
  


    $translations['eu']['Buscar'] = 'Bilatu';

    if ( isset( $translations[ $lang ][ $string ] ) ) {
      return $translations[ $lang ][ $string ];
    }
  }
  
  return $string;
});