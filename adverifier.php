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

require_once(ADVERIFIER__PLUGIN_DIR . 'class.adverifier.php');
require_once(ADVERIFIER__PLUGIN_DIR . 'admin/class.admin.php');
require_once(ADVERIFIER__PLUGIN_DIR . 'admin/class.serializer.php');

if (!function_exists('add_action')) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

register_activation_hook(__FILE__, array('Adverifier', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('Adverifier', 'plugin_deactivation'));

add_action('init', array('Adverifier', 'init'));
add_action('plugins_loaded', 'adverifier_admin_page');

function adverifier_admin_page() {
  $serializer = new Serializer();
  $serializer->init();

  $admin_page = new AdminAdverifier(new AdminAdverifierPage($serializer));
  $admin_page->init();
}
