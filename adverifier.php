<?php
/**
 * @package Advert Verifier
 */
/*
Plugin Name: Advert Verifier
Plugin URI: http://egalitate.md
Description: This plugin helps to exclude discriminating advertising and job offers.
Version: 4.0.1
Author URI: https://automattic.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: advert_verifier
*/

add_action('wp_enqueue_scripts','adverifier_load_scripts');

function create_ads_post_type() {
  // Labels array added inside the function and precedes args array
  $labels = array(
    'name'               => _x( 'Ads', 'post type general name' ),
    'singular_name'      => _x( 'Ad', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'Ad' ),
    'add_new_item'       => __( 'Add New Ad' ),
    'edit_item'          => __( 'Edit Ad' ),
    'new_item'           => __( 'New Ad' ),
    'all_items'          => __( 'All Ads' ),
    'view_item'          => __( 'View Ad' ),
    'search_items'       => __( 'Search ads' ),
    'not_found'          => __( 'No ads found' ),
    'not_found_in_trash' => __( 'No ads found in the Trash' ),
    'parent_item_colon'  => '',
    'menu_name'          => 'Ads'
  );

  // Args array
  $args = array(
    'labels'        => $labels,
    'description'   => 'Find ads with discriminatory content',
    'public'        => true,
    'menu_position' => 4,
    'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    'has_archive'   => true,
  );

  register_post_type( 'ads', $args );
}
add_action( 'init', 'create_ads_post_type' );

//Creating custom categories for Ads custom post
function ad_categories_definition() {
  // Labels array
  $labels = array(
    'name'              => _x( 'Ads Categories', 'taxonomy general name' ),
    'singular_name'     => _x( 'Ad  Category', 'taxonomy singular name' ),
    'search_items'      => __( 'Search Ad Categories' ),
    'all_items'         => __( 'All Ad Categories' ),
    'parent_item'       => __( 'Parent Ad Category' ),
    'parent_item_colon' => __( 'Parent Ad Category:' ),
    'edit_item'         => __( 'Edit Ad Category' ),
    'update_item'       => __( 'Update Ad Category' ),
    'add_new_item'      => __( 'Add New Ad Category' ),
    'new_item_name'     => __( 'New Ad Category' ),
    'menu_name'         => __( ' Ad Categories' ),
  );

  // Args array.
  $args = array(
    'labels' => $labels,
    'hierarchical' => true,
  );

  register_taxonomy( 'ad_category', 'ads', $args );
}

add_action( 'init', 'ad_categories_definition', 0 );

// Add form for the anonymous users.
add_shortcode('ad_post_form', 'ad_post_form_shortcode');

/**
 * Main functionality on the form submit.
 */
function ad_post_form_shortcode() {
  wp_register_script();
  static $loaded = false;
  if (!$loaded) {
    $loaded = true;

    print ad_post_form();
  }

  static $submitted = false;
  if (isset( $_POST['adverifier_form_submitted'] ) && wp_verify_nonce($_POST['adverifier_form_submitted'], 'adverifier_form_submit') && !$submitted) {
    $submitted = true;
    $content = strip_tags(trim($_POST['adverifier_form_content']));
    $title = __('Ad-' . time() . '-' . date('d-m-Y H:i:s'));

    $ad = array(
      'post_title' => $title,
      'post_content' => $content,
      'post_status' => 'private',
      'post_author' => 1,
      'post_type' => 'ads',
    );

//    $aid = wp_insert_post($ad); // Node is saved here.
//    update_post_meta ( $ad_id,'anonymous_user',$suq_quote_author);

    // Perform post validation.
    $categories = get_terms(array('taxonomy' => 'ad_category', 'hide_empty' => false,));
    $categories = adverifier_filter_terms($categories);

    $aid = 123;
    if ($aid) {
      wp_enqueue_script( 'ajax-script', plugins_url( 'adverifier.js', __FILE__ ), array('jquery'), null, true);
      wp_localize_script( 'ajax-script', 'adverifier', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) , 'aid' => $aid, 'categories' => $categories) );
    }
  }
}

/**
 * Form rendering which will hold ad data.
 *
 * @return string
 */
function ad_post_form() {
  $output = '<form id="adverifier-form" method="post" action="">';

  $output .= wp_nonce_field('adverifier_form_submit', 'adverifier_form_submitted');
  $output .= '<textarea id="adverifier-form-content" name="adverifier_form_content" /></textarea><br/>';

//  $output .= '<div class="g-recaptcha" data-sitekey="6LcYxTAUAAAAAKw0L6jRU4ok-brDW3BTFInVFj_z"></div><br/>';

  $output .= '<button type="submit" id="adverifier-form-submit" name="adverifier_form_submit" value="Verify Ad" class="btn medium white-col purple-bg green-bg-hover right">' . __('Verify Ad') . '</button>';

  $output .= '</form>';

  $popup = '<div id="adverifier-modal-results" title="' . __('Adverifier result') . '">';
  $popup .= '<div id="adverifier-result-message"></div>';
  $popup .= '</div>';

  $output .= $popup;

  return $output;
}

/**
 * Load jQuery UI scripts for the modal window display.
 */
function adverifier_load_scripts() {
  wp_enqueue_style('adverifier-css', plugins_url('adverifier.css', __FILE__));
  wp_register_script( 'jQuery', 'http://code.jquery.com/jquery-1.11.1.min.js', null, null, true );
  wp_enqueue_script('jQuery');
  wp_register_script( 'jQueryUI', 'http://code.jquery.com/ui/1.11.1/jquery-ui.min.js', null, null, true );
  wp_enqueue_script('jQueryUI');
}

/**
 * Filter discriminatory words.
 *
 * @param array $categories
 *   Collection of the taxonomy terms for Ad post type.
 *
 * @return array
 *   Filtered terms to be compared with.
 */
function adverifier_filter_terms($categories) {
  $terms = array();
  foreach ($categories as $category) {
    $terms[$category->slug] = $category->name;
  }

  return $terms;
}

add_action( 'wp_ajax_adverifier_save_statistics', 'adverifier_save_statistics' );
add_action( 'wp_ajax_nopriv_adverifier_save_statistics', 'adverifier_save_statistics' );

/**
 * Ajax callback to save data to statistics table and process message for the popup.
 */
function adverifier_save_statistics() {
  if (!empty($_POST['statistics'])) {
    $data = array_filter($_POST['statistics']);

    // Prepare result message.
    $output = '<div id="adverifier-result-message">';
    if (empty($data)) {
      $message = '<div class="adverifier-sign adverifier-success"></div>';
      $message .= '<div class="adverifier-message">' . __('This ad has no discriminatory words.') . '</div>';
    }
    else {
      $message = '<div class="adverifier-sign adverifier-fail"></div>';
      $message .= '<div class="adverifier-message">' . __('This ad has discriminatory words. Please revise your ad.') . '</div>';
    }
    $output .= "$message</div>";

    // Save data to tables
    $data['aid'] = $_POST['aid'];

    print $output;
  }
  exit();
}
