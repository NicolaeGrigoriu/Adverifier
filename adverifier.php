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

  // args array

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
