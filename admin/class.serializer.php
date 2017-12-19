<?php

/**
 * @file
 * Define crieria serializer functionality.
 */
class Serializer {
  public function init() {
    add_action('admin_post', array($this, 'action'));
  }

  public function action() {
    // Provide default page.
    $type = 'criteria';

    // Filter actions.
    if (!empty($_POST)) {
      if ($this->validate()) {
        if ($_POST['op'] == 'edit') {
          $this->edit();
        } else {
          $this->save();
        }
      }

      // Prepare path for redirect.
      $type = $_POST['type'];
      $type .= ($_POST['type'] == 'terms') ? '&cid=' . $_POST['cid'] : '';
    } elseif (!empty($_GET['op']) && $_GET['op'] == 'delete') {
      $this->delete();

      // Prepare path for redirect.
      $type = $_GET['type'];
      $type .= ($_GET['type'] == 'terms') ? '&cid=' . $_GET['cid'] : '';
    }
    // Redirect after action is complete.
    $path = '&type=' . $type . '&op=view';
    $this->redirect($path);
  }

  private function save() {
    if ($_POST['type'] == 'criteria') {
      $ro = sanitize_text_field($_POST['adverifier-criteria-ro']);
      $ru = sanitize_text_field($_POST['adverifier-criteria-ru']);
      $en = sanitize_text_field($_POST['adverifier-criteria-en']);

      $this->saveCriteria($ro, $ru, $en);
    } elseif ($_POST['type'] == 'terms') {
      $cid = sanitize_text_field($_POST['cid']);

      $ro = sanitize_text_field($_POST['adverifier-term-ro']);
      $ru = sanitize_text_field($_POST['adverifier-term-ru']);
      $en = sanitize_text_field($_POST['adverifier-term-en']);

      $this->addTerm($cid, $ro, $ru, $en);
    }
  }

  private function edit() {
    if ($_POST['type'] == 'criteria') {
      $cid = sanitize_text_field($_POST['cid']);

      $ro = sanitize_text_field($_POST['adverifier-criteria-ro']);
      $ru = sanitize_text_field($_POST['adverifier-criteria-ru']);
      $en = sanitize_text_field($_POST['adverifier-criteria-en']);

      $this->updateCriterion($cid, $ro, $ru, $en);
    } elseif ($_POST['type'] == 'terms') {
      $tid = sanitize_text_field($_POST['tid']);

      $ro = sanitize_text_field($_POST['adverifier-term-ro']);
      $ru = sanitize_text_field($_POST['adverifier-term-ru']);
      $en = sanitize_text_field($_POST['adverifier-term-en']);

      $this->updateTerm($tid, $ro, $ru, $en);
    }
  }

  private function delete() {
    $table = '';
    $key = '';

    if ($_GET['type'] == 'criteria') {
      $table = 'wp_criteria';
      $key = 'cid';
    } elseif ($_GET['type'] == 'terms') {
      $table = 'wp_criteria_terms';
      $key = 'tid';
    }

    if (!empty($table) && !empty($key) && !empty($_GET[$key])) {
      global $wpdb;
      $wpdb->delete($table, array($key => $_GET[$key]), array('%d'));
    }
  }

  private function validate() {
    if ($_POST['type'] == 'criteria') {
      $values = array_filter(array(
          sanitize_text_field($_POST['adverifier-criteria-ro']),
          sanitize_text_field($_POST['adverifier-criteria-ru']),
          sanitize_text_field($_POST['adverifier-criteria-en']),
        )
      );
    } elseif ($_POST['type'] == 'terms') {
      $values = array_filter(array(
          sanitize_text_field($_POST['adverifier-term-ro']),
          sanitize_text_field($_POST['adverifier-term-ru']),
          sanitize_text_field($_POST['adverifier-term-en']),
        )
      );
    }

    if (empty($values)) {
      return FALSE;
    }

    return TRUE;
  }

  private function redirect($path = '') {
    // To make the Coding Standards happy, we have to initialize this.
    if (!isset($_POST['_wp_http_referer'])) { // Input var okay.
      $_POST['_wp_http_referer'] = esc_html(admin_url('options-general.php') . '?page=adverifier');
    }

    // Sanitize the value of the $_POST collection for the Coding Standards.
    $url = sanitize_text_field(
      wp_unslash($_POST['_wp_http_referer']) // Input var okay.
    );

    if (!empty($path)) {
      $url .= $path;
    }
    // Finally, redirect back to the admin page.
    wp_safe_redirect(urldecode($url));
    exit;
  }

  private function saveCriteria($ro, $ru, $en) {
    global $wpdb;

    $wpdb->insert(
      'wp_criteria',
      array(
        'nume_RO' => $ro,
        'nume_RU' => $ru,
        'nume_EN' => $en,
      ),
      array('%s', '%s', '%s')
    );
  }

  private function addTerm($cid, $ro, $ru, $en) {
    global $wpdb;

    $wpdb->insert(
      'wp_criteria_terms',
      array(
        'cid' => $cid,
        'nume_RO' => $ro,
        'nume_RU' => $ru,
        'nume_EN' => $en,
      ),
      array('%d', '%s', '%s', '%s')
    );
  }

  private function updateTerm($tid, $ro, $ru, $en) {
    global $wpdb;

    $wpdb->update(
      'wp_criteria_terms',
      array(
        'nume_RO' => $ro,
        'nume_RU' => $ru,
        'nume_EN' => $en,
      ),
      array(
        'tid' => $tid,
      ),
      array('%s', '%s', '%s'),
      array('%d',)
    );
  }

  private function updateCriterion($cid, $ro, $ru, $en) {
    global $wpdb;

    $wpdb->update(
      'wp_criteria',
      array(
        'nume_RO' => $ro,
        'nume_RU' => $ru,
        'nume_EN' => $en,
      ),
      array(
        'cid' => $cid,
      ),
      array('%s', '%s', '%s'),
      array('%d')
    );
  }
}
