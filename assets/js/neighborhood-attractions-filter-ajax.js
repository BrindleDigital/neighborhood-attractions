jQuery(document).ready(function ($) {
    // filter on click
    $('.attraction-type-button').on('click', function () {
        $('.attraction-type-button').removeClass('active');
        $(this).addClass('active');

        $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            dataType: 'html',
            data: {
                action: 'filter_attractions',
                category: $(this).attr('data-slug'),
            },
            success: function (res) {
                $('.na-attractions-wrap').html(res);
            },
        });
    });

    // filter on load
    function filterAttractionsOnLoad() {
        $('.attraction-type-button').first().addClass('active');

        $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            dataType: 'html',
            data: {
                action: 'filter_attractions',
            },
            success: function (res) {
                $('.na-attractions-wrap').html(res);
            },
        });
    }

    $(window).on('load', filterAttractionsOnLoad);
});
