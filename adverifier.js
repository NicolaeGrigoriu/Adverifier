jQuery(document).ready(function ($) {
  // Move form inside the content body.
  $('#adverifier-wrapper').replaceWith($('#adverifier-form'));
  $('#adverifier-modal-results').dialog({
    autoOpen: false,
    minHeight: 200
  });
  $('#adverifier-form-submit').on('click', function (event) {
    event.preventDefault();
    var stat = {};
    // Content is validated here.
    Object.keys(adverifier.categories).forEach(function (category) {
      var content = $('#adverifier-form-content').val();
      var regExp = new RegExp("(<=\\s|\\b)" + category + "(?=[]\\b|\\s|$)", "gi");
      stat[category] = (content.match(regExp) || []).length;
    });

    // Open popup with loader.
    $('#adverifier-modal-results').dialog('open');
    $('#adverifier-result-message').replaceWith('<div class="loader"></div>');

    $.ajax({
      type: "post",
      url: adverifier.ajax_url,
      data: {
        'action': "adverifier_save_statistics",
        'statistics': stat,
        'aid': adverifier.aid
      },
      success: function (response) {
        // Populate popup with data from statistics.
        $('#adverifier-modal-results').find('.loader').replaceWith(response);
      }
    });
  });
});
