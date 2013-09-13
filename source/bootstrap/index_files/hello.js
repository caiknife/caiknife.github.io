(function ($) {
    
$(function(){
    var $win = $(window),
        $body = $('body'),
        $nav = $('.other-subnav'),
        navHeight = $('.navbar').first().height(),
        subnavHeight = $('.other-subnav').first().height(),
        subnavTop = $('.other-subnav').length && $('.other-subnav').offset().top - navHeight,
        marginTop = parseInt($body.css('margin-top'), 10);
        
        isFixed = 0;
        processScroll();
        
        $win.on('scroll', processScroll);
        
        function processScroll() {
            var i, scrollTop = $win.scrollTop();
            if (scrollTop >= subnavTop && !isFixed) {
                isFixed = 1;
                $nav.addClass('other-subnav-fixed');
                $body.css('margin-top', marginTop + subnavHeight + 'px');
            } else if (scrollTop <= subnavTop && isFixed) {
                isFixed = 0;
                $nav.removeClass('other-subnav-fixed');
                $body.css('margin-top', marginTop + 'px');
            }
        }
});

})(window.jQuery);