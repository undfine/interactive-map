<?php
/*
*  Plugin Name: Interactive Map
*/
if ( ! defined( 'ABSPATH' ) ) {
  die( 'Invalid request.' );
}


add_action( 'wp_enqueue_scripts', 'nmap_enqueue_scripts' );
function nmap_enqueue_scripts(){

  // Enqueue required jQuery libraries
  //wp_enqueue_script('jquery-ui-core');
  //wp_enqueue_script('jquery-ui-widget');
  //wp_enqueue_script('jquery-ui-mouse');
  //wp_enqueue_script('jquery-ui-draggable');
  //wp_enqueue_script('jquery-ui-accordion');
  //wp_enqueue_script('jquery-ui-autocomplete');
  //wp_enqueue_script('jquery-ui-slider');
  //wp_enqueue_script('jquery-ui-tabs');
  //wp_enqueue_script('jquery-ui-sortable');

  //wp_enqueue_script('jquery-ui-droppable');
  //wp_enqueue_script('jquery-ui-datepicker');
  //wp_enqueue_script('jquery-ui-resize');
  //wp_enqueue_script('jquery-ui-dialog');
  //wp_enqueue_script('jquery-ui-button');


  // Register plugin scripts
  wp_register_script('nmap_interactive_map_js', NMAP_PLUGIN_URL.'js/interactive_map.js', array('jquery'), true);
  wp_enqueue_script('nmap_interactive_map_js');

  // Register styles
  wp_register_style('nmap_homesites_css', NMAP_PLUGIN_URL.'css/interactive_map.css');
  wp_enqueue_style('nmap_homesites_css');
  wp_register_style('nmap_cursors_css', NMAP_PLUGIN_URL.'css/cursors.css');
  wp_enqueue_style('nmap_cursors_css');


  // Panzoom
  wp_register_script('nmap_panzoom_js', NMAP_PLUGIN_URL.'js/panzoom.min.js', true);
  wp_enqueue_script('nmap_panzoom_js');

  // ion.RangeSlider
  wp_register_script('ion_range_slider_js', NMAP_PLUGIN_URL.'js/ion.rangeSlider.min.js', true);
  wp_enqueue_script('ion_range_slider_js');
  wp_register_style('ion_range_slider_css', NMAP_PLUGIN_URL.'css/ion.rangeSlider.min.css');
  wp_enqueue_style('ion_range_slider_css');


  // TinySlider
  // wp_register_script('tiny_slider_js', 'https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js');
  // wp_enqueue_script('tiny_slider_js');
  // wp_register_style('tiny_slider_css', 'https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.3/tiny-slider.css');
  // wp_enqueue_style('tiny_slider_css');

}
