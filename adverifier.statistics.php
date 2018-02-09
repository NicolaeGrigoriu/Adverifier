<?php
/**
 * @file
 */
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class AdverifierStatistics {

  private $page;

  public function __construct($page) {
    $this->page = $page;
  }

  public function init() {
    add_action('admin_menu', array($this, 'add_statistics_page'));
  }

  // Init submenu page.
  public function add_statistics_page() {
    add_submenu_page(
      'edit.php?post_type=ads',
      'Ads statistics',
      'Ads statistics',
      'manage_options',
      'statistics',
      array($this->page, 'render')
    );
  }
}

class AdverifierPage {

  private $serializer;

  public function __construct($serializer) {
    $this->serializer = $serializer;
  }

  public function getSerializer() {
    return $this->serializer;
  }

  // Load js scripts.
  public function loadScripts() {
    wp_register_script();
    wp_enqueue_script('AdverifierStats', plugins_url('js/adverifier.statistics.js', __FILE__), array(), NULL, TRUE);

    wp_register_script('ChartJS', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js', NULL, NULL, TRUE);
    wp_enqueue_script('ChartJS');

    wp_register_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.11.19/jszip.js', NULL, NULL, TRUE);
    wp_enqueue_script('jszip');

    wp_register_script('SheetJS', 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.11.19/xlsx.full.min.js', NULL, NULL, TRUE);
    wp_enqueue_script('SheetJS');

    wp_enqueue_style('adverifier-css', plugins_url('css/adverifier.css', __FILE__));
  }

  public function render() {
    $this->loadScripts();

    $categories = get_terms('ad_category',
      array(
        'orderby'    => 'count',
        'hide_empty' => 0,
        'parent'     => 0,
      )
    );

    $terms      = array();
    $start      = $_GET['start'] ? date('Y-m-d', strtotime($_GET['start'])) : '';
    $end        = $_GET['end'] ? date('Y-m-d', strtotime($_GET['end'])) : '';
    $serializer = $this->getSerializer();
    foreach ($categories as $category) {
      $terms[$category->term_id] = array(
        'name'  => apply_filters('translate_text', $category->name),
        'tid'   => $category->term_id,
        'count' => $serializer->getTermCount($category->term_id, $start, $end),
        'color' => 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ', ' . rand(0, 255) . ', 0.2)',
      );
    }

    wp_localize_script('AdverifierStats', 'AdverifierStats', array(
      'categories' => $terms,
    ));

    include_once 'adverifier.statistics.form.php';
  }
}

class AdverifierStatSerializer {

  public function init() {
    add_action('admin_post', array($this, 'action'));
  }

  private function getTermChildren($tid) {
    $children = get_term_children($tid, 'ad_category');
    array_unshift($children, $tid);

    return $children;
  }

  public function getTermCount($tid, $start, $end) {
    global $wpdb;
    $terms = $this->getTermChildren($tid);
    $terms = "'" . implode("', '", $terms) . "'";

    $query = "SELECT COUNT(*) FROM $wpdb->term_relationships INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id WHERE $wpdb->term_relationships.term_taxonomy_id IN ({$terms})";
    $args = array();

    if ($start) {
      $query .= " AND $wpdb->posts.post_date >= %s";
      $args[] = $start;
    }
    if ($end) {
      $query .= " AND $wpdb->posts.post_date < %s";
      $args[] = $end;
    }

    $count = $wpdb->get_var($wpdb->prepare($query, $args));

    return $count;
  }

  public function action() {
    $path = '';
    if ($_POST['start']) {
      $path .= '&start=' . $_POST['start'];
    }

    if ($_POST['end']) {
      $path .= '&end=' . $_POST['end'];
    }

    $this->redirect($path);
  }

  private function redirect($path = '') {
    if (!isset($_POST['_wp_http_referer'])) {
      $_POST['_wp_http_referer'] = esc_html(admin_url('edit.php') . '?post_type=ads&page=statistics');
    }
    $url = sanitize_text_field(
      wp_unslash($_POST['_wp_http_referer'])
    );

    if (!empty($path)) {
      $url .= $path;
    }

    // Finally, redirect back to the admin page.
    wp_safe_redirect(htmlspecialchars_decode($url));
    exit();
  }
}
