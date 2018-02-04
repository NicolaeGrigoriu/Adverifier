<?php
$today = date("Y-m-d");
$start = date('Y-m-d', strtotime('-1 months', strtotime($today)));
?>

<div id="adverifier-statistics-wrapper">
  <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
    <input id="start-date" type="date" value="<?php echo ($_GET['start']) ? date('Y-m-d', strtotime($_GET['start'])) : $start; ?>">
    <input id="end-date" type="date" value="<?php echo ($_GET['end']) ? date('Y-m-d', strtotime($_GET['end'])) : $today; ?>">
    <?php $button = __('Filter'); ?>
    <button class="export" type="button"><?php print __('Export'); ?></button>
    <?php submit_button($button); ?>
    <input type="hidden" name="start" value="<?php echo $_GET['start']; ?>" />
    <input type="hidden" name="end" value="<?php echo $_GET['end']; ?>" />
    <canvas id="adverifier-statistics-container" ></canvas>
  </form>
</div>
