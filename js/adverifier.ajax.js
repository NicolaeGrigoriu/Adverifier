jQuery(document).ready(function ($) {
  $('#adverifier-modal-results').dialog({
    autoOpen: false,
    minHeight: 200
  });
  $('#adverifier-form-submit').on('click', function (event) {
    var stat = {};
    Object.values(adverifier.categories).forEach(function (category) {
      // Content is validated here.
      var content = $('#adverifier-form-content').val();
      // var regExp = new RegExp("(<=\\s|\\b)" + category + "(?=[]\\b|\\s|$)", "gi");
      var regExp = new RegExp("(" + category + ")", "gi");
      stat[category] = (content.match(regExp) || []).length;
      //(^\|[ \n\r\t.,'\"\+!?-]+)(програмни)([ \n\r\t.,'\"\+!?-]+\|$)
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
    event.preventDefault();
  });
});
