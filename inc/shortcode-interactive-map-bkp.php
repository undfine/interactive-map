<?php
/*
*  Plugin Name: Interactive Map
*/
if ( ! defined( 'ABSPATH' ) ) {
  die( 'Invalid request.' );
}

//Register Shorcode 'interactive_map'
add_shortcode( 'interactive_map', 'nmap_interactive_map_shortcode' );


// function to output "interactive_map" shortcode
function nmap_interactive_map_shortcode($atts) {

  // set up default parameters
  extract(shortcode_atts(array(
   'id' => get_the_ID()
  ), $atts));

  global $nmap_acf;

  wp_enqueue_script('nmap_interactive_map_js');
  wp_enqueue_style('nmap_homesites_styles');
  wp_enqueue_style('font-asesome');
  wp_enqueue_style('font-asesome-solid');


  // GET POST DATA FROM META FIELDS
  $maptitle = get_post_field( 'post_name', $id );
  $mapbase = get_post_meta($id, 'map_base', true);
  $maplayers = get_field('map_layers', $id);
  $svg = get_post_meta($id, 'map_code', true);
  $enable_controls = get_field('enable_map_controls', $id);
  $enable_toggles = get_field('enable_map_toggles', $id);
  $filter_terms = get_field( $nmap_acf['cpt-terms'], $id);

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
        'taxonomy' => $nmap_acf['cpt-terms'],
        'field' => 'term_id',
        'terms' => $filter_terms
  );
  $args = array (
    'post_type' => $nmap_acf['cpt'],
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

      //$html .= nmap_output_cpt_data();
      include NMAP_PLUGIN_DIR .'inc/map-points-single.php';

    endwhile; endif;
    wp_reset_postdata();

  $html .= '</div><div class="lot-screen"><div class="lot-inner"></div></div>';
  $html .= '</div>'; // close "interactive-map-container"

  return $html;
}
