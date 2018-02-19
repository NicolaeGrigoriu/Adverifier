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

define('ADVERIFIER__PLUGIN_DIR', plugin_dir_path(__FILE__));
require_once(ADVERIFIER__PLUGIN_DIR . 'adverifier.post.php');
require_once(ADVERIFIER__PLUGIN_DIR . 'adverifier.statistics.php');

if (!function_exists('add_action')) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

// register_activation_hook(__FILE__, array('Ads', 'activate'));

// Register ads post.
add_action('plugins_loaded', 'adverifier_load_post_type');
function adverifier_load_post_type() {
  $post = new Ads();
  $post->init();
}

// Add statistics page.
add_action('plugins_loaded', 'adverifier_statistics_page');
function adverifier_statistics_page() {
  $serializer = new AdverifierStatSerializer();
  $serializer->init();

  $stats = new AdverifierStatistics(new AdverifierPage($serializer));
  $stats->init();
}
