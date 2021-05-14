"use strict";

document.addEventListener('DOMContentLoaded', function () {
  var startMigrationButton = document.querySelector('a[data-action="sympose-start-migration"]');

  if (startMigrationButton !== null) {
    startMigrationButton.addEventListener('click', function () {
      startMigrationButton.nextElementSibling.classList.add('is-active');
      startMigrationButton.setAttribute('disabled', true);
      var url = wpApiSettings.root + 'sympose/v1/migrate';
      fetch(url, {
        method: "POST",
        body: JSON.stringify({
          version: startMigrationButton.dataset.version
        })
      }).then(function (response) {
        return response.json();
      }).then(function (result) {
        if (result.status === 200) {
          startMigrationButton.nextElementSibling.classList.remove('is-active');
          startMigrationButton.setAttribute('disabled', false);
          startMigrationButton.innerHTML = 'Done!';
        }
      });
    });
  }
});
"use strict";

jQuery(document).ready(function ($) {
  refreshPage();
  var form = $('form#sympose-quick-start'); // Next step

  $('form#sympose-quick-start .steps li a').on('click', function (e) {
    e.preventDefault();
    window.history.pushState({}, document.title, $(this).attr('href'));
    refreshPage();
  }); // Submit event

  $('form#sympose-quick-start .footer li').on('click', 'a', function (e) {
    e.preventDefault();
    var currentStep = $('form#sympose-quick-start').attr('data-id');

    if ($(e.target).parent().data('action') == 'next') {
      refreshPage(parseInt(currentStep) + 1);
    } else if ($(e.target).parent().data('action') == 'prev') {
      if (currentStep > 1) {
        refreshPage(parseInt(currentStep) - 1);
      }
    } else if ($(e.target).parent().data('action') == 'submit') {
      var formData = new FormData(document.querySelector('form#sympose-quick-start'));
      $.ajax({
        url: wpApiSettings.root + 'sympose/v1/quick_start_event',
        method: 'POST',
        processData: false,
        contentType: false,
        beforeSend: function beforeSend(xhr) {
          $('form#sympose-quick-start .spinner').addClass('is-active');
          xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
        },
        data: formData,
        success: function success(data) {
          $('form#sympose-quick-start .spinner').removeClass('is-active');
          $('.notice', form.parent()).remove();

          if (data.status === 400) {
            form.parent().prepend('<div class="notice notice-error is-dismissible"> <p>' + data.message + '</p> </div>');
          } else {
            form.parent().prepend('<div class="notice notice-success is-dismissible"> <p>' + data.message + '</p> </div>'); // Clean

            $('form#sympose-quick-start table tr[data-type=clone], form#sympose-quick-start table tr[data-type=first]').remove();
            $('form#sympose-quick-start .content .block table').map(function (key, val) {
              if ($('tr[data-type=first]', this).length < 1) {
                addRow(val, 'first', false);
              }
            });
            $('form#sympose-quick-start input').val('');
          } // Make sure that notices are visible.


          $('.sympose-setup-wizard > .content').animate({
            scrollTop: 0
          }, 400, 'swing');
        },
        error: function error() {}
      });
    }
  }); // Initally go through every table and add a row

  $('form#sympose-quick-start .content .block table').map(function (key, val) {
    if ($('tr[data-type=first]', this).length < 1) {
      addRow(val, 'first', false);
    }
  }); // On click new row

  $('form#sympose-quick-start .content').on('click', '.block table a[data-action="add"]', function (e) {
    e.preventDefault();
    var table = $(this).closest('table');
    addRow(table, 'clone', true);
  }); // On tab

  $('form#sympose-quick-start').on('keyup', function (e) {
    if ($(e.target).parents('tr').is(':last-child') && e.which == '9' && $(e.target).parents('td').is(':last-child')) {
      if (!e.shiftKey) {
        var table = $(e.target).closest('table');
        addRow(table, 'clone', true);
      }
    }
  }); // Delete

  $('form#sympose-quick-start .content .block table').on('click', 'a[data-action="delete"]', function (e) {
    e.preventDefault();
    $(this).closest('tr').remove();
    var formData = new FormData(document.querySelector('form#sympose-quick-start'));
    formData.append('save_data', 'yes');
    $.ajax({
      url: wpApiSettings.root + 'sympose/v1/quick_start_event',
      beforeSend: function beforeSend(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
      },
      method: 'POST',
      processData: false,
      contentType: false,
      data: formData,
      success: function success(data) {//
      }
    });
  }); // Save on every field change

  $('form#sympose-quick-start').on('change', function (e) {
    var formData = new FormData(document.querySelector('form#sympose-quick-start'));
    formData.append('save_data', 'yes');
    $.ajax({
      url: wpApiSettings.root + 'sympose/v1/quick_start_event',
      beforeSend: function beforeSend(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
      },
      method: 'POST',
      processData: false,
      contentType: false,
      data: formData,
      success: function success(data) {// TODO: Show data saved message.
      }
    });
  });
}); // Add row function

function addRow(table, type, focus) {
  var elem = jQuery('tr[data-type="initial"]', table);
  var lastRow = jQuery('tr:last-child', elem.closest('tbody')).attr('data-id');

  if (lastRow === 'initial') {
    lastRow = 0;
  }

  var newRow = parseInt(lastRow) + 1;
  var clonedElem = elem.clone().attr('data-type', type).attr('data-id', newRow);
  var fields = jQuery('input, select', clonedElem);
  fields.map(function (key, field) {
    jQuery(field).attr('name', jQuery(field).attr('name').replace('row', newRow));
  });
  clonedElem.appendTo(elem.closest('tbody'));

  if (focus === true) {
    jQuery('td:first-child input', clonedElem).focus();
  }
} // Refresh page, set the right content


function refreshPage(step) {
  var url = new URL(window.location.href);

  if (!step) {
    step = url.searchParams.get("step");
  } else {
    url.searchParams.set("step", step);
    window.history.pushState({}, document.title, url);
  }

  if (step) {
    jQuery('form#sympose-quick-start').attr('data-id', step);
  }

  updateSchedule(); // Focus on first input

  jQuery('form#sympose-quick-start .block[data-id=' + step + '] input:visible').first().focus();
} // Update schedule with people/organisations


function updateSchedule() {
  // Build the days
  var daysElem = jQuery('form#sympose-quick-start input#days');
  var initialTable = jQuery('form#sympose-quick-start table.schedule[data-type=initial]');
  var days = daysElem.val();

  if (days === 0 || !days || days === undefined) {
    days = 1;
  }

  var i; // Add schedules for every day

  for (i = 1; i <= days; i++) {
    // Check if exists
    var tableExists = jQuery('form#sympose-quick-start table.schedule[data-id=' + i + ']'); // If it doesn't exist..

    if (tableExists.length < 1) {
      var clone = initialTable.clone();
      clone.attr('data-type', 'clone');
      clone.attr('data-id', i); // Update title

      jQuery('.title', clone).text(jQuery('.title', clone).text() + ' ' + i); // Update names

      var fields = jQuery('input, select', clone);
      fields.map(function (key, field) {
        jQuery(field).attr('name', jQuery(field).attr('name').replace('initial', i));
      });
      clone.appendTo(initialTable.parent());
    }
  } // Add people/organisations


  var selectElements = jQuery('form#sympose-quick-start .content .block[data-id=4] select');
  selectElements.map(function (key, elem) {
    // Populate field.
    var elems = jQuery('tr:not([data-type=initial]) input[name="' + elem.dataset.type + '[]"]');
    var options = '';
    jQuery('option:first-child', elem).map(function (key, elem) {
      options += '<option value="0">' + elem.textContent + '</option>';
    });
    elems.map(function (key, item) {
      key++;
      var name = jQuery(item).val();

      if (name) {
        options += '<option value="' + key + '">' + name + '</option>';
      }
    });
    jQuery(elem).html(options); // Check for values.

    var selected = JSON.parse(elem.dataset.selected);

    if (selected.length > 0) {
      jQuery(elem).val(selected);
    }
  });
}
"use strict";

document.addEventListener('DOMContentLoaded', function () {
  var setupWizard = document.querySelector('.sympose-setup-wizard');

  if (setupWizard !== null) {
    resizeSetupWizard(); // Show

    setupWizard.style.display = 'flex';
    var buttons = setupWizard.querySelectorAll('ul.buttons li a');
    var notices = setupWizard.querySelector('.sympose-setup-wizard-notices');
    buttons.forEach(function (button) {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        var action = e.target.dataset.action;
        var headers = {
          'X-WP-Nonce': wpApiSettings.nonce
        };
        var url = '';
        var data = ''; // Sample Data

        if (action === 'setup-sample-data') {
          button.parentNode.querySelector('.spinner').style.display = 'block';
          button.parentNode.querySelector('.spinner').classList.add('is-active');
          url = wpApiSettings.root + 'sympose/v1/generate_sample_data';
        }

        if (action === 'skip-step') {
          url = wpApiSettings.root + 'sympose/v1/quick_start_event';
          data = {
            action: action
          };
          setupWizard.style.display = 'none';
        }

        if (action === 'quick-start') {
          setupWizard.querySelector('.wizard-content[data-type=quick-start]').style.display = 'block';
          setupWizard.querySelector('.wizard-content[data-type=introduction]').style.display = 'none';
        }

        fetch(url, {
          method: "POST",
          headers: headers,
          body: JSON.stringify(data)
        }).then(function (response) {
          return response.json();
        }).then(function (result) {
          if (result.status === 200) {
            var spinner = button.parentNode.querySelector('.spinner');

            if (spinner !== null) {
              button.parentNode.querySelector('.spinner').classList.remove('is-active');
              button.parentNode.querySelector('.spinner').style.display = 'none';
            }

            notices.innerHTML = '<div class="notice notice-success is-dismissible"> <p>' + result.message + '</p> </div>';
          }
        });
      });
    });
    var url = new URL(window.location.href);
    var step = url.searchParams.get("step");

    if (step) {
      setupWizard.querySelector('.wizard-content[data-type=quick-start]').style.display = 'block';
      setupWizard.querySelector('.wizard-content[data-type=introduction]').style.display = 'none';
    }
  }
});

var resizeSetupWizard = function resizeSetupWizard() {
  var setupWizard = document.querySelector('.sympose-setup-wizard');
  var wpContent = document.querySelector('#wpcontent');

  if (setupWizard !== null) {
    // Resize
    setupWizard.style.marginLeft = wpContent.offsetLeft + 'px';
  }
};

window.addEventListener('resize', resizeSetupWizard);
"use strict";

(function ($) {
  'use strict';

  $(function () {
    $('.sympose-generate-sample-data').on('click', 'input[type=submit]', function (e) {
      e.preventDefault();
      var spinner = $(this).siblings('span.spinner'); // Make Request to server

      $.ajax({
        url: wpApiSettings.root + 'sympose/v1/generate_sample_data',
        method: 'GET',
        beforeSend: function beforeSend(xhr) {
          spinner.addClass('is-active');
          xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
        }
      }).done(function (data) {
        if (data.status == 400) {
          $('.wrap h2').after('<div class="notice notice-error is-dismissible"> <p>' + data.message + '</p> </div>');
        } else {
          $('.wrap h2').after('<div class="notice notice-success is-dismissible"> <p>' + data.message + '</p> </div>');
        }

        spinner.removeClass('is-active');
        $(this).attr('disabled', 'disabled');
      });
    });
    var sortableElements = $('.cmb-row.sortable');

    if (sortableElements.length > 0) {
      $('ul.cmb2-list', sortableElements).sortable();
    }
  });
})(jQuery);