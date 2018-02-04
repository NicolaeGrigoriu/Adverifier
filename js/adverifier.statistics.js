jQuery(document).ready(function ($) {
  'use strict';
  // Pass data to hidden input.
  $("#start-date").change(function() {
    $('input[name="start"]').val(this.value);
  });
  $("#end-date").change(function() {
    $('input[name="end"]').val(this.value);
  });

  // Working with charts.
  var ctx = $("#adverifier-statistics-container");
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: $.map(AdverifierStats.categories, function(value) {
        return [value.name];
      }),
      datasets: [{
        label: '# de anunțuri',
        data: $.map(AdverifierStats.categories, function(value) {
          return [value.count];
        }),
        backgroundColor: $.map(AdverifierStats.categories, function(value) {
          return [value.color];
        }),
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero:true
          }
        }]
      }
    }
  });

  // Download data as xlsx.
  $('#adverifier-statistics-wrapper').find('button.export').on('click', function () {
    var period = $('input[name="start"]').val() + '-' + $('input[name="end"]').val();
    var data = [
        ['Perioada', period],
        ['Categorie', 'Anunțuri']
    ];
    for (var key in AdverifierStats.categories) {
      if (AdverifierStats.categories.hasOwnProperty(key)) {
        data.push([AdverifierStats.categories[key].name, AdverifierStats.categories[key].count]);
      }
    }

    var wb = {SheetNames:["Sheet1"], Sheets:{Sheet1:XLSX.utils.aoa_to_sheet(data)}};
    XLSX.writeFile(wb, 'Adverifier-' + period + '.xlsx');
  });
});
