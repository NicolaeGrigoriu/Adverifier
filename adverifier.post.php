<?php
/**
 * @file
 * Define post type ads and its category.
 */

class Ads {

  public static function activate() {
    $post = array(
      'post_title'     => __('[:en]Verify Ad[:ro]Verifică anunțul[:ru]Проверьте объявление[:]'),
      'post_name'      => 'verifica-anunt',
      'post_content'   => '<div id="adverifier-wrapper">[ad_post_form]</div>',
      'post_status'    => 'publish',
      'post_author'    => 1,
      'post_type'      => 'page',
      'comment_status' => 'closed',
      'ping_status'    => 'closed',
    );
    wp_insert_post($post);

    // Register ads post and categories.
    self::createPostType();
    self::createPostCategory();
    self::createPostCategoryTerms();
  }

  function init() {
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
    );

    register_taxonomy('ad_category', 'ads', $args);
  }

  public function createPostCategoryTerms() {
    $categories = array(
      'rasă'                                => array(),
      'culoare'                             => array(),
      'naţionalitate'                       => array(),
      'origine etnică'                      => array(
        'rom',
        'rus',
        'moldovean',
        'țigan',
        'negru',
      ),
      'limbă'                               => array(),
      'religie sau convingeri'              => array(),
      'avere'                               => array(),
      'statut HIV+'                         => array(),
      'domiciliu/ sediu'                    => array(),
      'apartenență/neapartenență sindicală' => array(),
      'origine socială'                     => array(),
      'orientarea sexuală'                  => array(),
      'sex'                                 => array(
        'director',
        'administrator',
        'profesor',
        'șef',
        'băieți',
        'domn',
        'bucătar',
        'paznic',
        'ospătar',
        'domnișoară',
        'domniță',
        'chelneriță',
        'recepționistă',
        'vânzătoare',
        'servitoare',
        'traducătoare',
        'asistentă',
        'studentă',
        'dădacă',
        'administratoare',
        'infermieră',
        'însărcinată',
        'maseoză',
        'femeie de serviciu',
        'croitoreasă',
        'absolventă',
        'contabilă',
        'coafeză',
        'secretara',
      ),
      'vârstă'                              => array(
        'tânăr',
        'tânără',
        'experiență',
        'ani',
      ),
      'dizabilitate'                        => array(),
      'stare a sănătății'                   => array(
        'vorbire clară',
        'voce plăcută',
        'rezistent la stres',
        'bună rezistență fizică',
        'stare bună de sănătate',
        'formă fizică bună',
        'lipsa maladii',
        'condiție fizică',
        'aspect exterior',
      ),
      'opinie'                              => array(),
      'apartenenţă politică'                => array(),
      'alt criteriu similar'                => array(
        'starea civilă',
        'prezența copiilor',
        'absența copiilor',
        'poză',
        'cazier',
        'antecedente penale',
        'permis auto',
        'viză de reședință',
        'loc de trai',
        'cetățenie',
        'fără vicii',
        'zodia',
      ),
    );

    foreach ($categories as $category => $subcategories) {
      $term = wp_insert_term($category, 'ad_category');
      if (!is_wp_error($term)) {
        foreach ($subcategories as $subcategory) {
          wp_insert_term($subcategory, 'ad_category', array('parent' => $term['term_id']));
        }
      }
    }
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
      if (!empty($category->name)) {
        $terms[$category->term_id] = $category->name;
      }
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

    $output .= '<div class="g-recaptcha" data-sitekey="6LcYxTAUAAAAAKw0L6jRU4ok-brDW3BTFInVFj_z"></div><br/>';

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

      // Link to the guide.
      $url_ro = site_url('wp-content/uploads/2016/04/Ghid-practic_pentru-web.pdf');
	  $url_ru = site_url('wp-content/uploads/2016/04/Ghid_rus_web.pdf');
	  
      // Info mail.
      $mail   = "mailto:info@egalitate.md?Subject=Info%20Adverifier";

      if (empty($data)) {
        $message = '<div class="adverifier-sign adverifier-success"></div>';
        $message .= '<div class="content-style adverifier-message">' . sprintf(__('[:en]This announcement does not contain criteria that might exclude or favour certain persons. Please revise the announcement. Just in case, please consult the <a target="_blank" href="%1$s">Guide on publishing recruitment advertisements</a> or contact us via e-mail <a target="_blank" href="%2$s">info@egalitate.md</a>.[:ro]Aparent acest anunț nu conține cerințe care ar putea exclude sau favoriza anumite persoane. Pentru orice eventualitate consultați <a target="_blank" href="%1$s">Ghidul privind întocmirea anunțurilor de recrutare</a> sau adresați o întrebare pe <a target="_blank" href="%2$s">info@egalitate.md</a>.[:ru]Данное объявление не содержит пункты, препятствующие или благоприятствующие трудоустройству определенных лиц. В случае надобности, обращайтесь к <a target="_blank" href="%3$s">Гиду по составлению вакансий</a> или по почте <a target="_blank" href="%2$s">info@egalitate.md</a>.[:]'), $url_ro, $mail, $url_ru) . '</div>';
      } else {
        $message = '<div class="adverifier-sign adverifier-fail"></div>';
        $message .= '<div class="content-style adverifier-message">' . sprintf(__('[:en]This announcement contains criteria that might exclude or favour certain persons. Please revise the announcement. For more details, please consult the <a target="_blank" href="%1$s">Guide on publishing recruitment advertisements</a> or contact us via e-mail <a target="_blank" href="%2$s">info@egalitate.md</a>[:ro]Acest anunț conține cerințe care ar putea exclude sau favoriza anumite persoane. Revizuiți anunțul. Pentru detalii consultați <a target="_blank" href="%1$s">Ghidul privind întocmirea anunțurilor de recrutare</a> sau adresați o întrebare pe <a target="_blank" href="%2$s">info@egalitate.md</a>.[:ru]Данное объявление содержит пункты, препятствующие или благоприятствующие трудоустройству определенных лиц. Пересмотрите данное объявление. Для дополнительной информации предлагаем вам обратиться к <a target="_blank" href="%3$s">Гиду по составлению вакансий</a> или по почте <a target="_blank" href="%2$s">info@egalitate.md</a>.[:]'), $url_ro, $mail, $url_ru) . '</div>';
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
}
