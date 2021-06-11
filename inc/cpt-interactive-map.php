<?php
/*
*  Plugin Name: Interactive Map
*/
if ( ! defined( 'ABSPATH' ) ) {
  die( 'Invalid request.' );
}

//Register Custom Post Types
if (!post_type_exists('interactive_map'))
    add_action( 'init', 'nmap_register_cpt_interactive_map', 30 );

function nmap_register_cpt_interactive_map() {

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



// Add shortcode to display on interactive map page
function nmap_shortcode_notice(){
      $screen = get_current_screen();

    //If not on the screen with ID 'edit-post' abort.
    if( $screen->post_type =='interactive_map' ) {
      $mapID = get_the_ID();

      ?>
      <div class="notice notice-info" style="padding:1em;">
        <span>Display the map using this shortcode:</span>
        <input value="[interactive_map id='<?= $mapID ?>']"
        name="shortcode"
        type="text" size="30"
        readonly
        onfocus="this.select()"/>
      </div>

     <?php
   }

 }
 add_action('admin_notices','nmap_shortcode_notice');
