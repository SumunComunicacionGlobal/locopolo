<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'ODOO_API_URL', 'https://erp.thelocopolo.com/jsonrpc/' );
define( 'ODOO_API_DB', 'locopolo_fabrica' );
define( 'ODOO_API_USER_NAME', 'web_bitevol' );
define( 'ODOO_API_USER_ID', 43);
define( 'ODOO_API_PASS', 'web_bitevol');

function after_xml_import($import_id, $import) {
    
	// is is products import
	if ($import_id == 1) {

		$trigger_url = get_home_url() . '/wp-load.php?import_key=SGeEN-3BSbm&import_id=7&action=trigger';
		// Call the next import's trigger URL (partners)
		wp_remote_get( $trigger_url );
    }

}
add_action('pmxi_after_xml_import', 'after_xml_import', 10, 2);


function fetch_products_api_data() {

	$args = array(
		'headers' => array(
			'Content-Type' => 'application/json',
		),
		'body'	=> '{
			"jsonrpc": "2.0",
			"method": "call",
			"params": {
				"service": "common",
				"method": "login",
				"args": ["'. ODOO_API_DB .'", "'. ODOO_API_USER_NAME .'", "'. ODOO_API_PASS .'"]
			}
		}',
	);

	$response = wp_remote_post( ODOO_API_URL, $args );

	// esperar un tiempo para que el servidor de Odoo procese la solicitud
	sleep(5);

	$args = array(
		'headers' => array(
			'Content-Type' => 'application/json',
		),
		'body'	=> '{
			"jsonrpc": "2.0",
			"method": "call",
			"params": {
				"service": "object",
				"method": "execute",
				"args": ["'. ODOO_API_DB .'", "'. ODOO_API_USER_ID .'", "'. ODOO_API_PASS .'", "product.product",
				"get_product_info", []]
			}
		}',
	);

	$response = wp_remote_post( ODOO_API_URL, $args );

	if (!is_wp_error($response)) {
		$body = wp_remote_retrieve_body($response);
		$decoded_body = json_decode($body);

		if ($decoded_body !== null && is_object($decoded_body)) {

			$uploads  = wp_upload_dir();
			$file_path = $uploads['basedir'] . '/odoo-data/products.json';
			$products = $decoded_body->result;
			$encoded_products = json_encode($products);
			file_put_contents($file_path, $encoded_products);
		}

	}

    return str_replace( $uploads['basedir'], $uploads['baseurl'], $file_path );

}


function fetch_partners_api_data() {

	$args = array(
		'headers' => array(
			'Content-Type' => 'application/json',
		),
		'body'	=> '{
			"jsonrpc": "2.0",
			"method": "call",
			"params": {
				"service": "common",
				"method": "login",
				"args": ["'. ODOO_API_DB .'", "'. ODOO_API_USER_NAME .'", "'. ODOO_API_PASS .'"]
			}
		}',
	);

	$response = wp_remote_post( ODOO_API_URL, $args );

	// esperar 5 segundos para que el servidor de Odoo procese la solicitud
	sleep(5);

	$args = array(
		'headers' => array(
			'Content-Type' => 'application/json',
		),
		'body'	=> '{
			"jsonrpc": "2.0",
			"method": "call",
			"params": {
				"service": "object",
				"method": "execute",
				"args": ["'. ODOO_API_DB .'", "'. ODOO_API_USER_ID .'", "'. ODOO_API_PASS .'", "res.partner",
				"get_partner_info", []]
			}
		}',
	);

	$response = wp_remote_post( ODOO_API_URL, $args );

	if (!is_wp_error($response)) {
		$body = wp_remote_retrieve_body($response);
		$decoded_body = json_decode($body);

		if ($decoded_body !== null && is_object($decoded_body)) {
			$uploads  = wp_upload_dir();
			$file_path = $uploads['basedir'] . '/odoo-data/partners.json';
			$partners = $decoded_body->result;
			$encoded_partners = json_encode($partners);
			file_put_contents($file_path, $encoded_partners);
		}
	}

    return str_replace( $uploads['basedir'], $uploads['baseurl'], $file_path );

}
