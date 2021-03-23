<?php

/*
Plugin Name: Interactive Map
Description: Custom map with interactive popups from property data. *Requires Advanced Custom Fields. Use shortcode [interactive_map]
Version: 1.4.1
Author: Compass
Author URI: compassad.com

*/
	if ( ! defined( 'ABSPATH' ) ) {
		die( 'Invalid request.' );
	}

	// Retrieve field names used of metadata of custom post type
	$acf_fields = array(
			'cpt' => 'property',
			'cpt-terms' => 'type',
			'name' => 'name',
			'caption' => 'caption',
			'type' => 'type',
			'status' => 'status',
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

	//include ('import_acf_fields.php');
	add_action( 'init', 'interactive_map_plugin_init', 0 );

	function interactive_map_plugin_init() {

		//Register Custom Post Types
		if (!post_type_exists('interactive_map')) register_cpt_interactive_map();

		//if (!post_type_exists('properties'))	register_cpt_properties();

		//Register scripts to be enqueued later within shortcode
		register_interactive_map_scripts();

		// Enqueue required jQuery libraries
		add_action( 'wp_enqueue_scripts', 'add_jquery_ui' );


		//Register Shorcode 'interactive_map'
		add_shortcode( 'interactive_map', 'interactive_map_shortcode' );

	}


	function add_jquery_ui() {
	        wp_enqueue_script('jquery-ui-core');
	        wp_enqueue_script('jquery-ui-widget');
	        wp_enqueue_script('jquery-ui-mouse');
	        //wp_enqueue_script('jquery-ui-accordion');
	        //wp_enqueue_script('jquery-ui-autocomplete');
	        //wp_enqueue_script('jquery-ui-slider');
	        //wp_enqueue_script('jquery-ui-tabs');
	        //wp_enqueue_script('jquery-ui-sortable');
	        wp_enqueue_script('jquery-ui-draggable');
	        //wp_enqueue_script('jquery-ui-droppable');
	        //wp_enqueue_script('jquery-ui-datepicker');
	        //wp_enqueue_script('jquery-ui-resize');
	        //wp_enqueue_script('jquery-ui-dialog');
	        //wp_enqueue_script('jquery-ui-button');
	}
	function register_interactive_map_scripts(){

		wp_register_script('map_points_js', plugin_dir_url( __FILE__ ).'js/map_points.js', array('jquery'), true);
		wp_register_script('map_points_admin_js', plugin_dir_url( __FILE__ ).'js/map_points_admin.js', array('jquery'), true);

		//Register plugin styles
		wp_register_script('interactive_map_js', plugin_dir_url( __FILE__ ).'js/interactive_map.js', array('jquery'), true);
		wp_register_style('homesites_styles', plugin_dir_url( __FILE__ ).'css/interactive_map.css');

		//Register Font Awesome homesites_styles
		//wp_register_style('font-awesome', plugin_dir_url( __FILE__ ).'css/fontawesome.min.css');
		//wp_register_style('font-awesome-solid', plugin_dir_url( __FILE__ ).'css/solid.min.css');

	}

	function register_cpt_properties() {
		global $acf_fields;

	  $labels = array(
	  'name'                  => _x( 'Properties', 'Post Type General Name', 'text_domain' ),
	  'singular_name'         => _x( 'Property', 'Post Type Singular Name', 'text_domain' ),
	  'menu_name'             => __( 'Properties', 'text_domain' ),
	  'name_admin_bar'        => __( 'Properties', 'text_domain' ),
	  'archives'              => __( 'Properties', 'text_domain' ),
	  'parent_item_colon'     => __( 'Parent Property:', 'text_domain' ),
	  'all_items'             => __( 'All Properties', 'text_domain' ),
	  'add_new_item'          => __( 'Add New Property', 'text_domain' ),
	  'add_new'               => __( 'Add New', 'text_domain' ),
	  'new_item'              => __( 'New Property', 'text_domain' ),
	  'edit_item'             => __( 'Edit Property', 'text_domain' ),
	  'update_item'           => __( 'Update Property', 'text_domain' ),
	  'view_item'             => __( 'View Property', 'text_domain' ),
	  'search_items'          => __( 'Search Property', 'text_domain' ),
	  'not_found'             => __( 'Not found', 'text_domain' ),
	  'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
	  'featured_image'        => __( 'Featured Image', 'text_domain' ),
	  'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
	  'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
	  'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
	  'insert_into_item'      => __( 'Insert into Property', 'text_domain' ),
	  'uploaded_to_this_item' => __( 'Uploaded to this Property', 'text_domain' ),
	  'items_list'            => __( 'Property list', 'text_domain' ),
	  'items_list_navigation' => __( 'Property list navigation', 'text_domain' ),
	  'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	    );
	    $args = array(
	  'label'                 => __( 'Properties', 'text_domain' ),
	  'description'           => __( 'Information regarding each homesite lot or map item including description and imagery.', 'text_domain' ),
	  'labels'                => $labels,
	  'supports'              => array( 'title', ),
	  'hierarchical'          => false,
	  'public'                => true,
	  'show_ui'               => true,
	  'show_in_menu'          => true,
	  'menu_position'         => 20,
	  'show_in_admin_bar'     => true,
	  'show_in_nav_menus'     => true,
	  'can_export'            => false,
	  'has_archive'           => false,
		'menu_icon'							=> 'dashicons-location',
	  'exclude_from_search'   => true,
	  'publicly_queryable'    => true,
	  'query_var'             => 'properties',
	  'capability_type'       => 'page',
	 );
	register_post_type( $acf_fields['cpt'], $args );

	// Add taxonomy to Maps
	$labels = array(
    'name' => _x( 'Property Types', 'taxonomy general name' ),
    'singular_name' => _x( 'Property Type', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Property Types' ),
    'all_items' => __( 'All Types' ),
    'parent_item' => __( 'Parent Property Type' ),
    'parent_item_colon' => __( 'Parent Property Type:' ),
    'edit_item' => __( 'Edit Property Type' ),
    'update_item' => __( 'Update Property Type' ),
    'add_new_item' => __( 'Add New Property Type' ),
    'new_item_name' => __( 'New Property Type Name' ),
    'menu_name' => __( 'Property Types' ),
  );

  register_taxonomy( $acf_fields['cpt-terms'], array($acf_fields['cpt']), array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true
  ));
	}



	function register_cpt_interactive_map() {

	    $labels = array(
	  'name'                  => _x( 'Interactive Map', 'Post Type General Name', 'text_domain' ),
	  'singular_name'         => _x( 'Interactive Map', 'Post Type Singular Name', 'text_domain' ),
	  'menu_name'             => __( 'Interactive Map', 'text_domain' ),
	  'name_admin_bar'        => __( 'Interactive Map', 'text_domain' ),
	  'archives'              => __( 'Interactive Map Archives', 'text_domain' ),
	  'parent_item_colon'     => __( 'Parent Interactive Maps:', 'text_domain' ),
	  'all_items'             => __( 'All Interactive Map', 'text_domain' ),
	  'add_new_item'          => __( 'Add New Interactive Map', 'text_domain' ),
	  'add_new'               => __( 'Add New', 'text_domain' ),
	  'new_item'              => __( 'New Interactive Map', 'text_domain' ),
	  'edit_item'             => __( 'Edit Interactive Map', 'text_domain' ),
	  'update_item'           => __( 'Update Interactive Map', 'text_domain' ),
	  'view_item'             => __( 'View Interactive Maps', 'text_domain' ),
	  'search_items'          => __( 'Search Interactive Maps', 'text_domain' ),
	  'not_found'             => __( 'Not found', 'text_domain' ),
	  'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
	  'featured_image'        => __( 'Base Map Image', 'text_domain' ),
	  'set_featured_image'    => __( 'Set Base Map image', 'text_domain' ),
	  'remove_featured_image' => __( 'Remove Base Map image', 'text_domain' ),
	  'use_featured_image'    => __( 'Use as Base Map image', 'text_domain' ),
	  'insert_into_item'      => __( 'Insert into Interactive Map', 'text_domain' ),
	  'uploaded_to_this_item' => __( 'Uploaded to this Interactive Map', 'text_domain' ),
	  'items_list'            => __( 'Interactive Map list', 'text_domain' ),
	  'items_list_navigation' => __( 'Interactive Map list navigation', 'text_domain' ),
	  'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	    );
	    $args = array(
	  'label'                 => __( 'Interactive Map', 'text_domain' ),
	  'description'           => __( 'Shows Map with interactive layers and clickable areas defined in SVG with popup data from Properties', 'text_domain' ),
	  'labels'                => $labels,
	  'supports'              => array( 'title', ),
	  'hierarchical'          => false,
	  'public'                => true,
	  'show_ui'               => true,
	  'show_in_menu'          => true,
	  'menu_position'         => 20,
	  'show_in_admin_bar'     => false,
	  'show_in_nav_menus'     => true,
	  'can_export'            => false,
	  'has_archive'           => false,
		'menu_icon'							=> 'dashicons-location-alt',
	  'exclude_from_search'   => true,
	  'publicly_queryable'    => true,
	  'query_var'             => 'interactive_map',
	  'capability_type'       => 'page',
	    );
	   register_post_type( 'interactive_map', $args );
	}



	// function interactive_map_points() {
	//
	// 	wp_enqueue_script('map_points_js');
	// 	wp_enqueue_script('map_points_admin_js');
	//
	// 	if( function_exists('acf_add_options_page') ) {
	//
	// 	acf_add_options_page( array(
	// 		'page_title' 	=> 'Interactive Map',
	// 		'menu_title' 	=> 'Interactive Map',
	// 		'menu_slug' 	=> 'interactive-map-settings',
	// 		'capability' 	=> 'edit_posts',
	// 		'redirect' 	=> false
	// 	));
	//
	// 	$basemap = get_field('map_file', 'option');
	// 	$points = get_field('map_points', 'option');
	// 	$projectlink = get_field('map_project_link','option');
	// 	$projectlinktext = get_field('map_project_link_text','option');
	// 	if ($projectlink) { $projectlink = '<div class="map-projectlink"><a href="'.$projectlink.'" target="_self">'.$projectlinktext.'</a></div>'; } else {$projectlink = '';}
	// 	$secondlink = get_field('map_second_link','option');
	// 	$secondlinktext = get_field('map_second_link_text','option');
	// 	if ($secondlink) { $secondlink = '<div class="map-secondlink"><a href="'.$secondlink.'" target="_self">'.$secondlinktext.'</a></div>'; } else {$secondlink = '';}
	// 	$count = 0;
	// 	$pointout = '';
	// 	$out = '<div class="interactive-map lay-of-the-land">';
	// 	if($basemap) { $out .= '<div class="map-base"><img src="'.$basemap.'" />'; }
	// 	$out .= '<div class="map-item-overlay"><ul>';
	// 	if($points){ foreach($points as $p) {
	// 		$title = $p['map_item_title'];
	// 		$desc = $p['map_item_desc'];
	// 		$position = $p['map_item_position'];
	// 		$thumb = $p['map_item_thumbnail'];
	// 		$maplink = $p['map_link'];
	// 		$isdisabled = $p['map_disable_link'];
	// 		if($isdisabled > 0) {$activestate = 'inactive';} else {$activestate = 'active';}
	// 		$image = wp_get_attachment_image_src($thumb, 'medium');
	// 		$posclass = '';
	// 		$pos = explode(',', $position);
	// 		$posx = $pos[0];
	// 		$posy = $pos[1];
	// 		if($posy > 50) {$posclass .= 'u';} else {$posclass .= 'l';}
	// 		if($posx > 50) {$posclass .= 'l';} else {$posclass .= 'r';}
	// 		$count++;
	// 		$letter = base_convert($count+9, 10,26);
	// 		$pointout.= '<li class="map-item map-item-'.$count.' map-item-'.$activestate.'" style="left:'.$posx.'%; top:'.$posy.'%;"><a href="#">'.$letter.'<span>'.$title.'</span></a><div class="map-item-inner '.$posclass.' map-item-inner-'.$activestate.'">';
	// 		if($maplink!='') { $pointout .= '<a href="'.$maplink.'" target="_self">'; }
	// 		if($image[0]) { $pointout .='<img src="'.$image[0].'" />'; }
	// 		if($title) { $pointout .= '<h6>'.$title.'</h6>'; }
	// 		if($desc) { $pointout .= '<p>'.$desc.'</p>'; }
	// 		if($maplink!='') { $pointout .= '</a>'; }
	// 		$pointout .= '</div></li>';
	// 	} }
	// 	$out.= $pointout.'</ul></div></div><div class="map-item-mobile-list"><ul>'.$pointout.'</ul></div>'.$projectlink.''.$secondlink.'<div class="clearfix"></div></div>';
	// 	return $out;
	// 	}
	// }


	// function to output "interactive_map" shortcode
	function interactive_map_shortcode($atts) {

		// set up default parameters
    extract(shortcode_atts(array(
     'id' => get_the_ID()
    ), $atts));

		global $acf_fields;

		wp_enqueue_script('interactive_map_js');
		wp_enqueue_style('homesites_styles');
		wp_enqueue_style('font-asesome');
		wp_enqueue_style('font-asesome-solid');


		// GET POST DATA FROM META FIELDS
		$maptitle = get_post_field( 'post_name', $id );
		$mapbase = get_post_meta($id, 'map_base', true);
		$maplayers = get_field('map_layers', $id);
		$svg = get_post_meta($id, 'map_code', true);
		$enable_controls = get_field('enable_map_controls', $id);
		$enable_toggles = get_field('enable_map_toggles', $id);
		$filter_terms = get_field( $acf_fields['cpt-terms'], $id);

		$showlotdata = (empty($filter_terms) || $filter_terms == null) ? 'hide' : '';
		$enabled = ($enable_controls == true)? ' enabled' : '';

		//Calculate Image Ratio to preserve aspect-ratio
		if ( $mapbase ) {
			$basemap = wp_get_attachment_image_src( $mapbase, 'full' );
			if($basemap[1] > 0 && $basemap[2] > 0) {
				$image_ratio = round(($basemap[2]/$basemap[1])*100, 1);
			} else {
				$image_ratio = 69.47;
			}
			$basemap = $basemap[0];
		} else {
			$image_ratio = 69.47;
			$basemap = plugin_dir_url( __FILE__ ).'assets/balsam-topo-map-v2.jpg';
		}

		// Setup blank html variables to be echoed later
		$maplayers_html = '';
		$toggles_html = '';
		$controls_html ='';
		$terms_arr = [];

		// Queue Map Layers
		if (is_array($maplayers) ){
			foreach ( $maplayers as $l ) {
				$checkvisible = $l['map_layer_visibility'];
				$layertitle = $l['map_layer_title'];

				$layertitle = str_replace(" ", "", $layertitle);
				$layertitle = str_replace("#", "", $layertitle);
				$layertitle = str_replace("-", "", $layertitle);

				$layertitle = strtoupper($layertitle);
				$layerimage = $l['map_layer_image'];
				$layericon = $l['map_layer_icon'];
				$layertype = $l['map_layer_type'];

				$visible = ($checkvisible == 1 ) ? 'active' : 'inactive';
				$toggletext = ($checkvisible == 1) ? 'HIDE ' : 'SHOW ';

				if ( $checkvisible == 1 ) {
					$visible = 'active';
					$toggletext = 'HIDE ';
				} else {
					$visible = 'inactive';
					$toggletext = 'SHOW ';
				}

				$maplayers_html .= '<div class="map-layer '.$visible.'" data-name="'.$layertitle.'" style="background-image:url('.$layerimage.'); padding-bottom:'.$image_ratio.'%;"></div>';

				if ( $enable_toggles == true ) {
					$toggles_html .= '<li class="map-toggle '.$visible.'" data-layer="'.$layertitle.'"><span class="icon icon-'.$layericon.'"></span>'.$toggletext.$layertitle.'</li>';
				}

				// Add layer type to terms array for query later
				if ( !empty($layertype) ) {
					array_push($terms_arr, $layertype);
				}

			}

		}

		$tax_query = array (
					'taxonomy' => $acf_fields['cpt-terms'],
					'field' => 'term_id',
					'terms' => $filter_terms
		);
		$args = array (
			'post_type' => $acf_fields['cpt'],
			'posts_per_page'=> -1,
			'orderby' => 'title',
			'order' => 'ASC'
		);

		if ( !empty($filter_terms) )
			$args['tax_query'] = array($tax_query);



		if ($enable_controls == true){
			$controls_html .= '<li class="map-zoom-toggle"><span class="icon icon-zoomin3"></span> Zoom Level';
			$controls_html .= '<span class="zoom-number active" data-zoom="zoom1">1</span> - ';
			$controls_html .= '<span class="zoom-number" data-zoom="zoom2">2</span> -';
			$controls_html .= '<span class="zoom-number" data-zoom="zoom3">3</span></li>';
		}


		// START HTML OUTPUT
		$html = '<div id="'.$maptitle.'" class="interactive-map-container" style="display:block; position:relative; width:100%; height:auto;">';

		if( $enable_controls == true || $enable_toggles == true) {
			$html .= '<div class="map-toggles"><ul>';
		  $html .= $toggles_html;
			$html .= $controls_html;
			$html .= '</ul></div>';
		}
		$html .= '<div class="homesite-map'.$enabled.'">';
		$html .='<div class="map-window" style="padding-bottom:'.$image_ratio.'%; opacity:0;">';
		$html .='<div class="map-viewport zoom1">';
		$html .='<div class="map-content-wrapper">';
		$html .='<div class="map-content" style="background-image:url('.$basemap.'); background-position:left top; background-size:100% 100%;">';
		$html .= $maplayers_html;
		$html .= '<div class="map-data">'.$svg.'</div>';
		//$html .= '<div class="map-info"><img src="'.plugin_dir_url( __FILE__ ).'assets/info_icon.png" /></div>';
		$html .= '<div class="map-quadrants disabled"><div class="map-q1"></div><div class="map-q2"></div><div class="map-q3"></div><div class="map-q4"></div>';
		$html .= '</div></div></div></div></div></div>';
		$html .= '<div class="homesite-lot-data '.$showlotdata.'">';

			$map_data = new WP_Query( $args );

			if ( $map_data->have_posts() ) : while ( $map_data->have_posts() ) :

				$map_data->the_post();

				$html .= output_lot_data();

			endwhile; endif;
			wp_reset_postdata();

		$html .= '</div><div class="lot-screen"><div class="lot-inner"></div></div>';
		$html .= '</div>'; // close "interactive-map-container"

		return $html;
	}


	function output_lot_data() {
			global $post;
			global $acf_fields;

			$lot_name = get_the_title();
			$lot_caption = get_field($acf_fields['caption']);
			$lot_number = get_field($acf_fields['number']);
			$lot_code = get_field($acf_fields['code']);
			$lot_sqft = get_field($acf_fields['sqft']);
			$lot_beds = get_field($acf_fields['beds']);
			$lot_baths = get_field($acf_fields['baths']);
			$lot_acres = get_field($acf_fields['acres']);
			$lot_address = get_field($acf_fields['address']);
			$lot_status = get_field($acf_fields['status']);
			$lot_price = get_field($acf_fields['price']);
			$lot_features = get_field($acf_fields['features']);
			$lot_description = get_the_content();
			$html_features = '';
			$html_file = '';

			//Format lot //
			$lot_id = (is_numeric($lot_number) && ($lot_number !== 0)) ? sprintf('%03d',$lot_number) : $lot_number;

			// Get property types (terms)
			$term_obj_list = get_the_terms( $post->ID, strval($acf_fields['cpt-terms']) );
			$terms_class_string = '';

			if (is_array($term_obj_list)){
				$terms_class_string = join(' ', wp_list_pluck($term_obj_list, 'slug'));
			} else {
				 $terms_class_string = $term_obj_list;
			}

			// construct type
			$is_lot_type = true; //get_field('m_bool_type');

			// get media capabilities and slider output
			$media_images = get_field($acf_fields['media']);
			$media_html = '';


			$media_count = (is_array($media_images)) ? count($media_images) : $media_count = 0;

			$ifnomedia = ' fullwidth';

			if ($media_count > 0) {
				$ifnomedia = '';

				$media_html .= '<div class="lot-images">';
				$media_html .= '<div class="lot-images-inner" data-slide="1" style="width:'.$media_count.'00%;">';

				foreach($media_images as $i) {
					$image_width = round((1/$media_count)*100, 4);
					$image_src = esc_url($i['sizes']['medium']);
					$image_src = isset($image_src) ? $image_src : esc_url($i['url']);
					$media_html .= '<div data-src="'.$image_src.'" style="width:'.$image_width.'%;"></div>';
				}

				$media_html .= '</div>';

				if($media_count > 1) {
					$media_html .= '<div class="lot-slider-nav"><a href="#" class="lot-slider-prev">&lsaquo;</a><a href="#" class="lot-slider-next">&rsaquo;</a></div>';
				}
				$media_html .= '</div>';
			}

				/* Lot File download link
				if ($lot_bool_file) {
					$html_file .= '<a class="lot-file" href="'.$lot_file.'" target="_blank"><span class="icon icon-Download"></span> Download</a>';
				}
				*/
				$html_details = '<ul class="lot-details">';
				//$html_details .= '<li>Address: '.$lot_address.'</li>';
				$html_details .= (!$lot_price) ? '' : '<li>$'.$lot_price.'</li>';
				$html_details .= (!$lot_sqft) ? '' : '<li>'.$lot_sqft.' SQFT</li>';


				//Format Bed/bath
				$beds = (is_numeric($lot_beds) && $lot_beds > 1) ? $lot_beds.' Bedrooms' : $lot_beds.' Bedroom';
				$baths = (is_numeric($lot_baths) && $lot_baths > 1) ? $lot_baths.' Bathrooms' : $lot_baths.' Bathroom';

				if (($lot_beds > 0) && ($lot_baths > 0)){

						$html_details .= '<li>'.$beds.' / '.$baths.'</li>';
				} else{
					$html_details .= (!$lot_beds) ? '' : '<li>'.$beds.'</li>';
					$html_details .= (!$lot_baths) ? '' : '<li>'.$baths.'</li>';
				}

				$html_details .= (!$lot_acres) ? '' : '<li>'.$lot_acres.' <span class="small-caps">acres</span></li>';
				$html_details .= '</ul>';

				// Lot Features
				if ($lot_features) {
					$html_features .= '<ul class="lot-features">';
					foreach($lot_features as $f) {
						$html_features .= '<li class="lot-feature">'.$f.'</li>';
					}
					$html_features .= '</ul>';
				}
				/*
				$item_bool_button = get_field('m_button');
				$item_button_link = get_field('m_button_link');
				$item_button_text = get_field('m_button_text');
				$item_button_target = get_field('m_button_target');

				$button_target = ($item_button_target) ? '_self' : '_blank';
				$button_out = '';

				if ($item_bool_button && $item_button_link) {
					$button_out .= '<div class="item-button-holder">';
					$button_out .= '<a href="'.$item_button_link.'" class="item-button btn" target="'.$button_target.'">';
					$button_out .= $item_button_text;
					$button_out .= '<span class="icon icon-ArrowRight"></span></a></div>';
				}
				*/

				$html = '<div id="'.$lot_id.'" class="lot-holder '.$terms_class_string.'" data-type="'.$terms_class_string.'">';
				$html .= $media_html;
				$html .= '<div class="lot-content '.$ifnomedia.'">';
				$html .= '<h3 class="lot-name">'.$lot_name.'</h3>';
				$html .= ($lot_caption != false) ? '<h4 class="lot-caption">'.$lot_caption.'</h4>' : '<h4 class="lot-caption">'.$lot_address.'</h4>';
				//$html .= '<li class="lot-size">'.$lot_acres.' <span>acres</span></li>';
				$html .= $html_details;
				//$html .= '<div class="lot-description">'.$lot_description.'</div>';
				$html .= $html_file;
				$html .= '</div><i class="close fa fa-times" aria-hidden="true"></i>';
				$html .= '</div>';

		return $html;
	}
?>
