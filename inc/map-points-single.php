<?php
/*
*  Plugin Name: Interactive Map
*/
if ( ! defined( 'ABSPATH' ) ) {
  die( 'Invalid request.' );
}

// This echoes the html for individual map points
// called within a loop in shortcode-interactive-map

    global $post;
    global $nmap_acf;

    $lot_name         = get_the_title();
    $lot_thumb        = get_the_post_thumbnail_url();
    $lot_description  = get_the_content();
    $lot_url          = get_post_permalink();
    $lot_caption      = get_field($nmap_acf['caption']);
    $lot_number       = get_field($nmap_acf['number']);
    $lot_code         = get_field($nmap_acf['code']);
    $lot_sqft         = get_field($nmap_acf['sqft']);
    $lot_beds         = get_field($nmap_acf['beds']);
    $lot_baths        = get_field($nmap_acf['baths']);
    $lot_acres        = get_field($nmap_acf['acres']);
    $lot_address      = get_field($nmap_acf['address']);
    $lot_status       = get_field($nmap_acf['status']);
    $lot_price        = get_field($nmap_acf['price']);
    $lot_features     = get_field($nmap_acf['features']);
    $html_features    = '';


    //Format lot
    //$lot_id = (is_numeric($lot_number) && ($lot_number !== 0)) ? sprintf('%03d',$lot_number) : $lot_number;
    $lot_id = $lot_number;

    // Get property types (terms)
    $term_obj_list = get_the_terms( $post->ID, strval($nmap_acf['cpt-terms']) );
    $terms_class_string = '';

    if (is_array($term_obj_list)){
      $terms_class_string = join(' ', wp_list_pluck($term_obj_list, 'slug'));
    } else {
       $terms_class_string = $term_obj_list;
    }

    // construct type
    $is_lot_type = true; //get_field('m_bool_type');


    // lot featured img
    $featured_img = (!$lot_thumb) ? '' : '<div class="lot-thumb" style="background-image:url('.$lot_thumb.');"></div>';

    // get media capabilities and slider output
    $media_images = get_field($nmap_acf['media']);
    $media_html = '';


    $media_count = (is_array($media_images)) ? count($media_images) : $media_count = 0;

    $ifnomedia = ' nomedia';

    if ($media_count > 0) {
      $ifnomedia = '';

      $media_html .= '<div class="lot-images">';
      $media_html .= '<div class="lot-images-inner" data-slide="1" style="width:'.$media_count.'00%;">';

      foreach($media_images as $i) {
        $image_width = round((1/$media_count)*100, 4);
        $image_src = esc_url($i['sizes']['large']);
        $image_src = isset($image_src) ? $image_src : esc_url($i['url']);
        $media_html .= '<div data-src="'.$image_src.'" style="width:'.$image_width.'%;"></div>';
      }

      $media_html .= '</div>';
      $media_html .= '</div>';
    }

      $html_details = '<ul class="lot-details">';
      //$html_details .= (!$lot_price)      ? '' : '<li>$'.$lot_price.'</li>';


      //Format Bed/bath
      $beds = (is_numeric($lot_beds) && $lot_beds > 1) ? $lot_beds.' Bedrooms' : $lot_beds.' Bedroom';
      $baths = (is_numeric($lot_baths) && $lot_baths > 1) ? $lot_baths.' Bathrooms' : $lot_baths.' Bathroom';

      if (($lot_beds > 0) && ($lot_baths > 0)){
          $html_details .= '<li>'.$beds.' / '.$baths.'</li>';
      } else{
        $html_details .= (!$lot_beds) ? '' : '<li>'.$beds.'</li>';
        $html_details .= (!$lot_baths) ? '' : '<li>'.$baths.'</li>';
      }

      $html_details .= (!$lot_sqft)  ? '' : '<li class="sqft">'.$lot_sqft.' SQFT</li>';
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


      $html_out= '<div id="'.$lot_id.'" class="lot-holder '.$terms_class_string.'" data-type="'.$terms_class_string.'">';
      $html_out.= $featured_img; //$media_html;

      $html_out.= '<div class="lot-content">';
        $html_out.= '<h3 class="lot-name">'.$lot_name.'</h3>';
        $html_out.= (!$lot_price) ? '' : '<h4 class="lot-price">$'.$lot_price.'</h4>';
        $html_out.= $html_details;
      $html_out.= '</div>';

      // hidden content used in popup
      $html_out.= '<div class="popup-content" style="display:none;">';
        $html_out.= $media_html;
        $html_out.= '<div class="lot-content">';
          $html_out.= '<h3 class="lot-name">'.$lot_name.'</h3>';
          $html_out.= (!$lot_price) ? '' : '<h4 class="lot-price">$'.$lot_price.'</h4>';
          $html_out.= $html_details;
          //$html_out.= $html_features;
          $html_out.= '<a href="'.$lot_url.'" class="view-more-btn">View Property</a>';
        $html_out.= '</div>';
      $html_out.= '</div>';



      $html_out.= '</div>';

  echo $html_out;
