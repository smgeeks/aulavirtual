(function ($) {
    $(document).ready(function () {
        $('.stm-member-tab a').click(function (e) {
            e.preventDefault();
            var id = $(this).attr('href');
            $('.stm-member-tab li').removeClass('current');
            $(this).parent().addClass('current');
            $('.stm-member-content__item').removeClass('active');
            $('.stm-member-content').find(id).addClass('active');
        })
    });
})(jQuery);