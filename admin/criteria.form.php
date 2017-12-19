<div id="adverifier-criteria-wrapper">
  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
  <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
    <table>
      <thead>
      <tr>
        <th>Nume RO</th>
        <th>Nume RU</th>
        <th>Nume EN</th>
        <th>Operații</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($this->getCriteria() as $criterion): ?>
          <tr class="criterion-row">
            <td class="criterion criterion-ro"><?php print $criterion['nume_RO']; ?></td>
            <td class="criterion criterion-ru"><?php print $criterion['nume_RU']; ?></td>
            <td class="criterion criterion-en"><?php print $criterion['nume_EN']; ?></td>
            <td><a href="<?php echo esc_html(admin_url('options-general.php')) . '?page=adverifier&type=terms&cid=' . $criterion['cid']; ?>&op=view">Listează termeni</a></td>
            <td><a href="<?php echo esc_html(admin_url('admin-post.php')) . '?page=adverifier&type=criteria&cid=' . $criterion['cid'] . '&op=delete'; ?>">Șterge criteriu</a></td>
              <td><a href="<?php echo esc_html(admin_url('options-general.php')) . '?page=adverifier&type=criteria&cid=' . $criterion['cid']; ?>&op=edit">Editează criteriu</a></td>
          </tr>
      <?php endforeach; ?>
      <tr>
        <?php $value = ($_GET['cid'] && $_GET['op'] == 'edit') ? $this->getCriterion($_GET['cid']) : FALSE; ?>
        <td>
          <input type="text" name="adverifier-criteria-ro" value="<? echo $value ? $value['nume_RO']: ''; ?>"/>
        </td>
        <td>
          <input type="text" name="adverifier-criteria-ru" value="<? echo $value ? $value['nume_RU']: ''; ?>"/>
        </td>
        <td>
          <input type="text" name="adverifier-criteria-en" value="<? echo $value ? $value['nume_EN']: ''; ?>"/>
        </td>
      </tr>
      </tbody>
    </table>
    <input type="hidden" name="cid" value="<?php echo $_GET['cid']; ?>" />
    <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" />
    <input type="hidden" name="op" value="<?php echo $_GET['op']; ?>" />
    <?php $button = $_GET['op'] == 'edit' ? 'Editează criteriu' : 'Adaugă criteriu'; ?>
    <?php submit_button($button); ?>
  </form>
</div>
