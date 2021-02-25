jQuery(document).ready(function ($) {

    refreshPage();

    const form = $('form#sympose-quick-start');

    // Next step
    $('form#sympose-quick-start .steps li a').on('click', function (e) {
        e.preventDefault();

        window.history.pushState({}, document.title, $(this).attr('href'));

        refreshPage();
    });

    // Submit event
    $('form#sympose-quick-start .footer li').on('click', 'a', function (e) {
        e.preventDefault()

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
                url: wpApiSettings.root+'sympose/v1/quick_start_event',
                method: 'POST',
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    $('form#sympose-quick-start .spinner').addClass('is-active');
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                },
                data: formData,
                success: (data) => {
                    $('form#sympose-quick-start .spinner').removeClass('is-active');

                    $('.notice', form.parent()).remove();

                    if (data.status === 400) {
                        form.parent().prepend('<div class="notice notice-error is-dismissible"> <p>'+data.message+'</p> </div>');
                    } else {
                        form.parent().prepend('<div class="notice notice-success is-dismissible"> <p>'+data.message+'</p> </div>');

                        // Clean
                        $('form#sympose-quick-start table tr[data-type=clone], form#sympose-quick-start table tr[data-type=first]').remove();

                        $('form#sympose-quick-start .content .block table').map(function(key, val) {
                            if ($('tr[data-type=first]', this).length < 1) {
                                addRow(val, 'first', false);
                            }
                        });

                        $('form#sympose-quick-start input').val('');
                    }

                    // Make sure that notices are visible.
                    $('.sympose-setup-wizard > .content').animate({
                        scrollTop: 0
                    }, 400, 'swing');


                },
                error: () => {

                }
            })
        }
    });

    // Initally go through every table and add a row
    $('form#sympose-quick-start .content .block table').map(function(key, val) {
        if ($('tr[data-type=first]', this).length < 1) {
            addRow(val, 'first', false);
        }
    });

    // On click new row
    $('form#sympose-quick-start .content').on('click', '.block table a[data-action="add"]', function(e) {
        e.preventDefault();
        var table = $(this).closest('table');
        addRow(table, 'clone', true);
    });

    // On tab
    $('form#sympose-quick-start').on('keyup', function(e) {
        if ($(e.target).parents('tr').is(':last-child') && e.which == '9' && $(e.target).parents('td').is(':last-child')) {
            if (!e.shiftKey) {
                var table = $(e.target).closest('table');
                addRow(table, 'clone', true);
            }
        }
    });

    // Delete
    $('form#sympose-quick-start .content .block table').on('click', 'a[data-action="delete"]', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();

        var formData = new FormData(document.querySelector('form#sympose-quick-start'));
        formData.append('save_data', 'yes');

        $.ajax({
            url: wpApiSettings.root+'sympose/v1/quick_start_event',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            success: function(data) {
                console.log(data)
            }
        })
    });

    // Save on every field change
    $('form#sympose-quick-start').on('change', function(e) {
        var formData = new FormData(document.querySelector('form#sympose-quick-start'));

        formData.append('save_data', 'yes');

        $.ajax({
            url: wpApiSettings.root+'sympose/v1/quick_start_event',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            success: function(data) {
                // TODO: Show data saved message.
            }
        })
    });

});

// Add row function
function addRow(table, type, focus) {
    var elem = jQuery('tr[data-type="initial"]', table);

    var lastRow = jQuery('tr:last-child', elem.closest('tbody')).attr('data-id');
    if (lastRow === 'initial') {
        lastRow = 0;
    }

    let newRow = parseInt(lastRow) + 1;

    var clonedElem = elem.clone().attr('data-type', type).attr('data-id', newRow);

    let fields  = jQuery('input, select', clonedElem);
    fields.map((key, field) => {
        jQuery(field).attr('name', jQuery(field).attr('name').replace('row', newRow));
    })

    clonedElem.appendTo(elem.closest('tbody'));

    if (focus === true) {
        jQuery('td:first-child input', clonedElem).focus();
    }
}

// Refresh page, set the right content
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

    updateSchedule();

    // Focus on first input

    jQuery('form#sympose-quick-start .block[data-id='+step+'] input:visible').first().focus();
}

// Update schedule with people/organisations
function updateSchedule() {

    // Build the days
    const daysElem = jQuery('form#sympose-quick-start input#days');

    const initialTable = jQuery('form#sympose-quick-start table.schedule[data-type=initial]');

    let days = daysElem.val();

    if (days === 0 || !days || days === undefined) {
        days = 1;
    }

    let i;

    // Add schedules for every day
    for (i = 1; i <= days; i++) {

        // Check if exists
        let tableExists = jQuery('form#sympose-quick-start table.schedule[data-id='+i+']');

        // If it doesn't exist..
        if (tableExists.length < 1) {
            let clone = initialTable.clone();

            clone.attr('data-type', 'clone');
            clone.attr('data-id', i);

            // Update title
            jQuery('.title', clone).text(jQuery('.title', clone).text() + ' ' + i);

            // Update names
            let fields  = jQuery('input, select', clone);
            fields.map((key, field) => {
                jQuery(field).attr('name', jQuery(field).attr('name').replace('initial', i));
            })

            clone.appendTo(initialTable.parent());
        }
    }

    // Add people/organisations
    const selectElements = jQuery('form#sympose-quick-start .content .block[data-id=4] select');

    selectElements.map((key, elem) => {
        // Populate field.
        let elems = jQuery('tr:not([data-type=initial]) input[name="'+elem.dataset.type+'[]"]');

        let options = '';

        jQuery('option:first-child', elem).map((key, elem) => {
            options += '<option value="0">'+elem.textContent+'</option>';
        });

        elems.map((key, item) => {
            key++;
            let name = jQuery(item).val();
            if (name) {
                options += '<option value="'+key+'">'+name+'</option>';
            }
        })
        jQuery(elem).html(options);

        // Check for values.
        const selected = JSON.parse(elem.dataset.selected);
        if (selected.length > 0) {
            jQuery(elem).val(selected);
        }
    });


}
