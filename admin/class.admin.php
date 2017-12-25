<?php

class AdminAdverifier {

  private $criteria_admin_page;
  private $ads_admin_page;

  public function __construct($criteria_admin_page, $ads_admin_page) {
    $this->criteria_admin_page = $criteria_admin_page;
    $this->ads_admin_page = $ads_admin_page;
  }

  public function init() {
    add_action('admin_menu', array($this, 'add_options_page'));
    add_action('admin_head', array($this, 'add_admin_head'));
  }

  public function add_options_page() {
    add_menu_page(
      'Adverifier',
      'Advert Verifier',
      'manage_options',
      'adverifier'

    );
    add_submenu_page(
      'adverifier',
      'Anunțuri',
      'Anunțuri',
      'manage_options',
      'adverifier_ads',
      array($this->ads_admin_page, 'render')
    );
    add_submenu_page(
      'adverifier',
      'Criterii',
      'Criterii',
      'manage_options',
      'adverifier',
      array($this->criteria_admin_page, 'render')
    );
  }

  public function add_admin_head() {
    print "<link rel='stylesheet' type='text/css' href='"  . WP_PLUGIN_URL . "/adverifier/admin/adverifier.admin.css' />\n";
  }
}

class AdminAdverifierCriteria {
  public function render() {
    if (!empty($_GET['type']) && $_GET['type'] == 'terms') {
      include_once 'terms.form.php';
    }
    else {
      include_once 'criteria.form.php';
    }
  }

  public function getCriteria() {
    global $wpdb;
    $criteria = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}criteria", ARRAY_A);

    return $criteria;
  }

  public function getCriterion($cid) {
    global $wpdb;
    $criteria = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}criteria WHERE cid = $cid", ARRAY_A);

    return $criteria;
  }

  public function getTerms($cid) {
    global $wpdb;
    $terms = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}criteria_terms WHERE cid = $cid", ARRAY_A);

    return $terms;
  }

  public function getTerm($cid, $tid) {
    global $wpdb;
    $term = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}criteria_terms WHERE cid = $cid AND tid = $tid", ARRAY_A);

    return $term;
  }
}

class AdminAdverifierAds {
  public function render() {
    include_once 'ads.form.php';
  }
}
