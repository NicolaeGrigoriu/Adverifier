<div id="adverifier-terms-wrapper">
  <?php $criteria = $this->getCriterion(esc_html($_GET['cid'])); ?>
  <h1>Termeni în criteriul <i><?php echo $criteria['nume_RO']; ?></i></h1>
  <a href="<?php echo esc_html(admin_url('options-general.php')); ?>?page=adverifier&type=criteria&op=view  ">Vizualizare categorii</a>
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
      <?php foreach($this->getTerms(esc_html($_GET['cid'])) as $term): ?>
        <tr data-tid="<?php echo $term['tid']; ?>" class="criterion-row">
          <td class="criterion term-ro"><?php print $term['nume_RO']; ?></td>
          <td class="criterion term-ru"><?php print $term['nume_RU']; ?></td>
          <td class="criterion term-en"><?php print $term['nume_EN']; ?></td>
          <td><a href="<?php echo esc_html(admin_url('options-general.php')) . '?page=adverifier&type=terms&cid='. $_GET['cid'] . '&tid=' . $term['tid']; ?>&op=edit">Editează termenul</a></td>
          <td><a href="<?php echo esc_html(admin_url('admin-post.php')) . '?page=adverifier&type=terms&cid='. $_GET['cid'] . '&tid=' . $term['tid'] . '&op=delete'; ?>">Șterge termen</a></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <?php $subcategory = ($_GET['tid'] && $_GET['op'] == 'edit') ? $this->getTerm($_GET['cid'], $_GET['tid']) : FALSE; ?>
        <td>
          <input type="text" name="adverifier-term-ro" value="<?php echo $subcategory ? $subcategory['nume_RO'] : ''; ?>"/>
        </td>
        <td>
          <input type="text" name="adverifier-term-ru" value="<?php echo $subcategory ? $subcategory['nume_RU'] : ''; ?>"/>
        </td>
        <td>
          <input type="text" name="adverifier-term-en" value="<?php echo $subcategory ? $subcategory['nume_EN'] : ''; ?>"/>
        </td>
      </tr>
      </tbody>
    </table>
    <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" />
    <input type="hidden" name="cid" value="<?php echo $_GET['cid']; ?>" />
    <input type="hidden" name="tid" value="<?php echo $_GET['tid']; ?>" />
    <input type="hidden" name="op" value="<?php echo $_GET['op']; ?>" />
    <?php $button = $_GET['op'] == 'edit' ? 'Editează termen' : 'Adaugă termen'; ?>
    <?php submit_button($button); ?>
  </form>
</div>
