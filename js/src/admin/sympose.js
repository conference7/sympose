(function ($) {
    'use strict';

    $(function () {

        $('.sympose-generate-sample-data').on('click', 'input[type=submit]', function (e) {
            e.preventDefault()

            var spinner = $(this).siblings('span.spinner');


            // Make Request to server
            $.ajax({
                url: wpApiSettings.root + 'sympose/v1/generate_sample_data',
                method: 'GET',
                beforeSend: function (xhr) {
                    spinner.addClass('is-active')
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                }
            }).done(function (data) {
                if (data.status == 400) {
                    $('.wrap h2').after('<div class="notice notice-error is-dismissible"> <p>'+data.message+'</p> </div>');
                } else {
                    $('.wrap h2').after('<div class="notice notice-success is-dismissible"> <p>'+data.message+'</p> </div>');
                }
                spinner.removeClass('is-active')
                $(this).attr('disabled', 'disabled');
            })
        });


        var sortableElements = $('.cmb-row.sortable');

        if (sortableElements.length > 0) {
            $('ul.cmb2-list', sortableElements).sortable();
        }

    })


})(jQuery);
