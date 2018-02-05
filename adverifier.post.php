<?php
/**
 * @file
 * Define post type ads and its category.
 */

class Ads {

  public function init() {
    // Register ads post and categories.
    add_action('init', array($this, 'createPostType'));
    add_action('init', array($this, 'createPostCategory'));

    // Load styles and js scripts.
    add_action('wp_enqueue_scripts', array($this, 'loadScripts'));

    // Add form for the anonymous users.
    add_shortcode('ad_post_form', array('Ads', 'postFormShortcode'));

    // Register save post functionality.
    add_action('wp_ajax_adverifier_save_post', array($this, 'savePost'));
    add_action('wp_ajax_nopriv_adverifier_save_post', array($this, 'savePost'));
  }

  /**
   * Creates Ads post type.
   */
  public function createPostType() {
    // Labels array added inside the function and precedes args array
    $labels = array(
      'name'               => _x('Ads', 'post type general name'),
      'singular_name'      => _x('Ad', 'post type singular name'),
      'add_new'            => _x('Add New', 'Ad'),
      'add_new_item'       => __('Add New Ad'),
      'edit_item'          => __('Edit Ad'),
      'new_item'           => __('New Ad'),
      'all_items'          => __('All Ads'),
      'view_item'          => __('View Ad'),
      'search_items'       => __('Search ads'),
      'not_found'          => __('No ads found'),
      'not_found_in_trash' => __('No ads found in the Trash'),
      'parent_item_colon'  => '',
      'menu_name'          => 'Ads',
    );

    // Args array
    $args = array(
      'labels'        => $labels,
      'description'   => 'Find ads with discriminatory content',
      'public'        => TRUE,
      'menu_position' => 4,
      'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
      'has_archive'   => TRUE,
    );

    register_post_type('ads', $args);
  }

  /**
   * Creates custom categories for Ads custom post
   */
  public function createPostCategory() {
    // Labels array
    $labels = array(
      'name'              => _x('Ads Categories', 'taxonomy general name'),
      'singular_name'     => _x('Ad  Category', 'taxonomy singular name'),
      'search_items'      => __('Search Ad Categories'),
      'all_items'         => __('All Ad Categories'),
      'parent_item'       => __('Parent Ad Category'),
      'parent_item_colon' => __('Parent Ad Category:'),
      'edit_item'         => __('Edit Ad Category'),
      'update_item'       => __('Update Ad Category'),
      'add_new_item'      => __('Add New Ad Category'),
      'new_item_name'     => __('New Ad Category'),
      'menu_name'         => __('Ad Categories'),
    );

    // Args array.
    $args = array(
      'labels'                => $labels,
      'hierarchical'          => TRUE,
      'update_count_callback' => array(&$this, 'updatePostCount'),
    );

    register_taxonomy('ad_category', 'ads', $args);
  }

  /**
   * Load jQuery UI scripts for the modal window display.
   */
  public function loadScripts() {
    wp_enqueue_style('adverifier-css', plugins_url('css/adverifier.css', __FILE__));
    wp_enqueue_style('highlight-css', plugins_url('css/highlight.css', __FILE__));
    wp_register_script('jQuery', 'http://code.jquery.com/jquery-1.11.1.min.js', NULL, NULL, TRUE);
    wp_enqueue_script('jQuery');
    wp_register_script('jQueryUI', 'http://code.jquery.com/ui/1.11.1/jquery-ui.min.js', NULL, NULL, TRUE);
    wp_enqueue_script('jQueryUI');
  }

  /**
   * Main functionality on the form submit.
   */
  public static function postFormShortcode() {
    wp_register_script();
    wp_enqueue_script('Adverifier', plugins_url('js/adverifier.form.js', __FILE__), array(), NULL, TRUE);
    wp_enqueue_script('Highlighter', plugins_url('js/highlight.js', __FILE__), array(), NULL, TRUE);

    static $loaded = FALSE;
    if (!$loaded) {
      $loaded = TRUE;
      print self::postForm();
    }

    $categories = get_terms(array(
      'taxonomy'   => 'ad_category',
      'hide_empty' => FALSE,
    ));
    $categories = self::filterTerms($categories);
    wp_localize_script('Adverifier', 'adverifier', array(
      'categories'  => $categories,
      'ajax_url'    => admin_url('admin-ajax.php'),
      'action'      => 'adverifier_save_post',
      '_ajax_nonce' => 'adverifier_save_post',
    ));
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
  public static function filterTerms($categories) {
    $terms = array();
    foreach ($categories as $category) {
      $terms[$category->term_id] = $category->name;
    }

    return $terms;
  }

  /**
   * Form rendering which will hold ad data.
   *
   * @return string
   */
  public static function postForm() {
    $output = '<form id="adverifier-form" method="post" action="">';

    $output .= wp_nonce_field('adverifier_form_submit', 'adverifier_form_submitted');
    $output .= '<textarea id="adverifier-form-content" name="adverifier_form_content" /></textarea><br/>';

    //  $output .= '<div class="g-recaptcha" data-sitekey="6LcYxTAUAAAAAKw0L6jRU4ok-brDW3BTFInVFj_z"></div><br/>';

    $output .= '<button type="submit" id="adverifier-form-submit" name="adverifier_form_submit" value="Verify Ad" class="btn medium white-col purple-bg green-bg-hover right">' . __('[:en]Verify Ad[:ro]Verifică anunțul[:ru]Проверьте обявление[:]') . '</button>';

    $output .= '</form>';

    $popup = '<div id="adverifier-modal-results" title="' . __('[:en]Adverifier result[:ro]Rezultat[:ru]Результат[:]') . '">';
    $popup .= '<div id="adverifier-result-message"></div>';
    $popup .= '</div>';

    $output .= $popup;

    return $output;
  }

  /**
   * Ajax callback to save data to statistics table and process message for the
   * popup.
   */
  public function savePost() {
    if (!empty($_POST['result'])) {
      $data = array_filter($_POST['result']);

      // Prepare result message.
      $output = '<div id="adverifier-result-message">';
      if (empty($data)) {
        $message = '<div class="adverifier-sign adverifier-success"></div>';
        $message .= '<div class="adverifier-message">' . __('[:en]This ad has no discriminatory words[:ro]Acest anunț nu conține cuvinte discriminatorii[:ru]Это обявление не содержит дискриминируюшее слова.[:]') . '</div>';
      } else {
        $message = '<div class="adverifier-sign adverifier-fail"></div>';
        $message .= '<div class="adverifier-message">' . __('[:en]This ad has discriminatory words[:ro]Acest anunț conține cuvinte discriminatorii[:ru]Это обявление содержит дискриминируюшее слова.[:]') . '</div>';
      }
      $output .= "$message</div>";

      $aid = $this->saveAd($_POST['content']);
      wp_set_post_terms($aid, array_keys($data), 'ad_category', TRUE);

      print $output;
    }
    exit();
  }

  /**
   * Save posted ad for further processing.
   *
   * @param string $content
   *   Content from the textfield.
   *
   * @return int post id.
   */
  private function saveAd($content) {
    $title = __('Ad-' . time() . '-' . date('d-m-Y H:i:s'));

    $ad = array(
      'post_title'   => $title,
      'post_content' => $content,
      'post_status'  => 'private',
      'post_author'  => 1,
      'post_type'    => 'ads',
      'post_category',
    );

    $aid = wp_insert_post($ad);

    return $aid;
  }

  public function updatePostCount($terms, $taxonomy) {
    global $wpdb;
    foreach ( (array) $terms as $term ) {
      $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term ) );

      do_action( 'edit_term_taxonomy', $term, $taxonomy );
      $wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
      do_action( 'edited_term_taxonomy', $term, $taxonomy );
    }
  }
}
