<?php
if ( !defined( 'ABSPATH' ) ) exit;
/*Description: This class registers a new custom post type
  Version: 1.0
  Tested up to: 5.6
*/

class NMAP_PostType{


    function __construct() {

      add_action( 'init', [ $this, 'load' ]);
    }

    function load(){

      // Set variables and field names used for metadata of custom post type
      $cpt_names = array(
          'cpt' => 'property',
          'cpt_plural' => 'properties',
          'cpt_terms' => array('type','status')
        );

        if ( !post_type_exists( $cpt_names['cpt']) ) $this->register_new_cpt($cpt_names);
    }


    function register_new_cpt($cpt_names) {
      extract($cpt_names);
      $singular = ucfirst($cpt);
      $plural = ucfirst($cpt_plural);

  	  $labels = array(
    	  'name'                  => _x( $plural, 'Post Type General Name', 'compassad' ),
    	  'singular_name'         => _x( $singular, 'Post Type Singular Name', 'compassad' ),
    	  'menu_name'             => __( $plural, 'compassad' ),
    	  'name_admin_bar'        => __( $plural, 'compassad' ),
    	  'archives'              => __( $plural, 'compassad' ),
    	  'all_items'             => __( 'All '.$plural, 'compassad' ),
    	  'add_new_item'          => __( 'Add New '.$singular, 'compassad' ),
    	  'add_new'               => __( 'Add New', 'compassad' ),
    	  'new_item'              => __( 'New '.$singular, 'compassad' ),
    	  'edit_item'             => __( 'Edit '.$singular, 'compassad' ),
    	  'update_item'           => __( 'Update '.$singular, 'compassad' ),
    	  'view_item'             => __( 'View '.$singular, 'compassad' ),
    	  'search_items'          => __( 'Search '.$singular, 'compassad' ),
    	  'not_found'             => __( $singular.' not found', 'compassad' ),
    	  'not_found_in_trash'    => __( $singular.' not found in Trash', 'compassad' ),
    	  'featured_image'        => __( 'Featured Image', 'compassad' ),
    	  'set_featured_image'    => __( 'Set featured image', 'compassad' ),
    	  'remove_featured_image' => __( 'Remove featured image', 'compassad' ),
    	  'use_featured_image'    => __( 'Use as featured image', 'compassad' ),
    	  'insert_into_item'      => __( 'Insert into '.$singular, 'compassad' ),
    	  'uploaded_to_this_item' => __( 'Uploaded to this '.$singular, 'compassad' ),
    	  'items_list'            => __( $singular.' list', 'compassad' ),
    	  'items_list_navigation' => __( $singular.' list navigation', 'compassad' ),
    	  'filter_items_list'     => __( 'Filter items list', 'compassad' ),
  	  );

  	  $args = array(
    	  'label'                 => __( $plural, 'compassad' ),
    	  'description'           => __( $singular.' Information.', 'compassad' ),
    	  'labels'                => $labels,
    	  'supports'              => array( 'title','editor', 'thumbnail' ),
    	  'hierarchical'          => false,
    	  'public'                => true,
    	  'show_ui'               => true,
    	  'show_in_menu'          => true,
    	  'menu_position'         => 20,
    	  'show_in_admin_bar'     => true,
    	  'show_in_nav_menus'     => true,
    	  'can_export'            => true,
    	  'has_archive'           => false,
    		'menu_icon'							=> 'dashicons-location',
    	  'exclude_from_search'   => true,
    	  'publicly_queryable'    => true,
    	  'query_var'             => $cpt_plural,
    	  'capability_type'       => 'post',
    	 );

      register_post_type( $cpt, $args );

      if (is_array($cpt_terms)){
        foreach($cpt_terms as $term){
            if (!taxonomy_exists($term))
              $this->register_new_cpt_taxonomy($cpt, $term);
        }
      }

  	}

    function register_new_cpt_taxonomy($cpt, $term){
      //splice terms name
      $term_name = $cpt.'_'.$term;
      $term_single = ucfirst($cpt).' '.ucfirst($term);

    	// Add taxonomy to Post Type
    	$labels = array(
        'name' => _x( $term_single, 'taxonomy general name' ),
        'singular_name' => _x( $term_single, 'taxonomy singular name' ),
        'search_items' =>  __( 'Search '.$term_single ),
        'all_items' => __( 'All Types' ),
        'parent_item' => __( 'Parent '.$term_single ),
        'parent_item_colon' => __( 'Parent '.$term_single.':' ),
        'edit_item' => __( 'Edit '.$term_single ),
        'update_item' => __( 'Update '.$term_single ),
        'add_new_item' => __( 'Add New '.$term_single ),
        'new_item_name' => __( 'New '.$term_single.' Name' ),
        'menu_name' => __( $term_single ),
      );

      register_taxonomy( $term_name, array($cpt), array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true
      ));

    }

}
new NMAP_PostType();

?>
