jQuery(document).ready(function ($) {
  // Move form inside the content body.
  let $form = $('#adverifier-form');
  $('#adverifier-wrapper').replaceWith($form);

  $('#adverifier-modal-results').dialog({
    autoOpen: false,
    minHeight: 200
  });
  $form.submit(function(event) {
    event.preventDefault();
    let stat = {};
    // Content is validated here.
    let $formContent = $('#adverifier-form-content');
    let content = $formContent.val();
    Object.values(adverifier.categories).forEach(function (category) {
      let strLower = category.toLowerCase();
      this.statistics[strLower] = 0;
      for (let i = 0; i < this.content.length; i++) {
        if (this.content[i].startsWith(strLower)) {
          this.statistics[strLower]++;
        }
      }
    }, {content: content.toLowerCase().split(' '), statistics: stat});

    // Convert object properties to array values.
    let arr = Object.keys(adverifier.categories).map(function (key) { return adverifier.categories[key]; });
    // Highlight matched words.

    $formContent.highlightWithinTextarea({
      highlight: arr
    });

    // Open popup with loader.
    $('#adverifier-modal-results').dialog('open');
    $('#adverifier-result-message').replaceWith('<div class="loader"></div>');

    // Save data and expose the result.
    $.ajax({
      type: "post",
      url: adverifier.ajax_url,
      data: {
        'action': "adverifier_save_statistics",
        'result': stat,
        'content': content
      },
      success: function (response) {
        // Populate popup with data from statistics.
        $('#adverifier-modal-results').find('.loader').replaceWith(response);
      }
    });
  });
});
