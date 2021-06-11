<?php

/*
Plugin Name: Interactive Map
Description: Custom map with interactive popups from property data. *Requires Advanced Custom Fields. Use shortcode [interactive_map]
Version: 1.4.1
Author: Compass
Author URI: compassad.com

Prefix: nmap
*/

	if ( ! defined( 'ABSPATH' ) ) {
		die( 'Invalid request.' );
	}

	define( 'NMAP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'NMAP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

	// Setup meta field names used by custom post type
	$nmap_acf = array(
			'cpt' => 'arts_portfolio_item',
			'cpt-terms' => 'arts_portfolio_category',
			'name' => 'name',
			'caption' => 'caption',
			'type' => 'arts_portfolio_category',
			'status' => 'property_status',
			'price' => 'listprice',
			'description' => 'description',
			'address' => 'address1',
			'address_city' => 'city',
			'address_state' => 'state',
			'address_zip' => 'zip',
			'number' => 'lotnumber',
			'code' => 'mls',
			'features' => 'features',
			'acres' => 'acreage',
			'beds' => 'bedrooms',
			'baths' => 'bathrooms',
			'halfbaths' => 'halfbaths',
			'elevation' => 'elevation',
			'sqft' => 'squarefootage',
			'button_bool' => 'button_bool',
			'button_link' => 'button_link',
			'button_text' => 'button_text',
			'button_target' => 'button_target',
			'media' => 'photo_gallery',
	);

	require_once NMAP_PLUGIN_DIR .'inc/frontend.php';
	require_once NMAP_PLUGIN_DIR .'inc/cpt-interactive-map.php';
	require_once NMAP_PLUGIN_DIR .'inc/shortcode-interactive-map.php';

	// Post type and taxonomies are already registered in the theme
	//require_once NMAP_PLUGIN_DIR .'inc/class-register-cpt.php';
