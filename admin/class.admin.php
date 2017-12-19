<?php

class AdminAdverifier {

  private $submenu_page;

  public function __construct($submenu_page) {
    $this->submenu_page = $submenu_page;
  }

  public function init() {
    add_action('admin_menu', array($this, 'add_options_page'));
  }

  public function add_options_page() {
    add_options_page(
      'Criterii',
      'Advert Verifier',
      'manage_options',
      'adverifier',
      array($this->submenu_page, 'render')
    );
  }
}

class AdminAdverifierPage {
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
    $criteria = $wpdb->get_results('SELECT * FROM wp_criteria', ARRAY_A);

    return $criteria;
  }

  public function getCriterion($cid) {
    global $wpdb;
    $criteria = $wpdb->get_row("SELECT * FROM wp_criteria WHERE cid = $cid", ARRAY_A);

    return $criteria;
  }

  public function getTerms($cid) {
    global $wpdb;
    $terms = $wpdb->get_results("SELECT * FROM wp_criteria_terms WHERE cid = $cid", ARRAY_A);

    return $terms;
  }

  public function getTerm($cid, $tid) {
    global $wpdb;
    $term = $wpdb->get_row("SELECT * FROM wp_criteria_terms WHERE cid = $cid AND tid = $tid", ARRAY_A);

    return $term;
  }
}
