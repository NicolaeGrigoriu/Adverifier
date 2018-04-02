jQuery(document).ready(function ($) {
  // Move form inside the content body.
  let $form = $('#adverifier-form');
  $('#adverifier-wrapper').replaceWith($form);

  $('#adverifier-modal-results').dialog({
    autoOpen: false,
    minHeight: 200,
    minWidth: 690,
    dialogClass: 'adverifier-result-title'
  });

  $('#adverifier-form-submit').on('click', function (event) {
    event.preventDefault();

    let $formContent = $('#adverifier-form-content');
    let content = $formContent.val();

    // Content is validated here.
    let filterContent = content.toLowerCase();
    filterContent = removeAccents(filterContent);

    let stat = {};
    for (let cid in adverifier.categories) {
      if (adverifier.categories.hasOwnProperty(cid)) {
        let category = adverifier.categories[cid].toLowerCase();
        stat[cid] = 0;
        let needle = removeAccents(category);
        if (filterContent.includes(needle)) {
          stat[cid]++;
        }
      }
    }

    // Convert object properties to array values.
    let arr = Object.keys(adverifier.categories).map(function (key) {
      return adverifier.categories[key];
    });

    // Highlight matched words.
/*    $formContent.highlightWithinTextarea({
      highlight: arr
    });*/
    $formContent.highlightWithinTextarea({
      highlight: ['voce placuta', 'ani']
    });


    // Open popup with loader.
    $('#adverifier-modal-results').dialog('open');
    $('#adverifier-result-message').replaceWith('<div class="loader"></div>');

    // Save data and expose the result.
    $.ajax({
      type: "post",
      url: adverifier.ajax_url,
      data: {
        'action': adverifier.action,
        '_ajax_nonce': adverifier._ajax_nonce,
        'result': stat,
        'content': content
      },
      success: function (response) {
        // Populate popup with data from statistics.
        $('#adverifier-modal-results').find('.loader').replaceWith(response);
      }
    });
  });

  function removeAccents(str) {
    let convMap = {
      'ă': 'a',
      'â': 'a',
      'î': 'i',
      'ș': 's',
      'ț': 't'
    };
    for (let i in convMap) {
      str = str.replace(new RegExp(i, "g"), convMap[i]);
    }
    return str;
  }
});
