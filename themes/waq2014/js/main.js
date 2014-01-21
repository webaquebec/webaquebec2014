var LIBEO;

(function($, window, document, undefined) {

    // On document ready
    $(document).ready(function() {

        // Check if the 'WAQ' object is defined
        if (typeof WAQ === 'object') {

        	LIBEO = WAQ;

        	WAQ.Constants = {
        		name            : 'WAQ',
        		onClickClass    : 'isClicked',
        		onHoverClass    : 'isHover',
        		onZoomClass     : 'l-zoomed',
        		fontPrefixClass : 'l-font',
        		isOpenClass     : 'isOpen',
        		isActiveClass   : 'isActive',
        		extLinksClass   : 'external'
        	};

            // Instantiate and store objects
            if( ! $('html').hasClass('lt-ie9') ){
                WAQ.SnapMenu         = WAQ.Common.createObjects($('#nav-main'), SnapMenu, {
                    adjustTopOffset : 45,
                    customNavigation : true,
                    breakpointsSelector : '.l-page-wrapper > section',
                });
                WAQ.SnapMenuSchedule = WAQ.Common.createObjects($('.days-buttons'), SnapMenu, {
                    adjustTopOffset : -133,
                    adjustBottomOffset : -133,
                    delimiterElement : $('.js-slider'),
                });
            }
            WAQ.Schedule = WAQ.Common.createObjects($('.schedule'), Schedule);
            WAQ.Slideshow = WAQ.Common.createObjects($('.blog-wrapper'), Slideshow);
            WAQ.MobileMenu = WAQ.Common.createObjects($('#nav-main'), MobileMenu);
            WAQ.CustomSchedule = WAQ.Common.createObjects($('.schedule-wrapper'), CustomSchedule);
            WAQ.Countdown = WAQ.Common.createObjects($('.event-stats'), Countdown);
            WAQ.GoogleMap = new CustomGmap('#gmap');

            if($('#gmap').length > 0){
                google.maps.event.addListenerOnce(WAQ.GoogleMap.map, 'idle', function() {
                    GoogleMapResize();
                });
            }

            // Common functions
            WAQ.Common.goToContent($('.l-a11y'));
            WAQ.Common.betterFocus(['a', 'button', 'input', 'h2']);
            WAQ.Common.checkForKeyboardInput();
            WAQ.Common.checkFontSize();
            WAQ.Common.externalLinks();
        }

        // Scroll to active hash on page load
        var hash = window.location.hash;
        if ( hash != '' ) {

            // Source : http://stackoverflow.com/questions/3659072/jquery-disable-anchor-jump-when-loading-a-page
            window.scrollTo(0, $( hash ).offset().top - 64);
            setTimeout(function() {
                window.scrollTo(0, $( hash ).offset().top - 64);
            }, 1);

            // Update active menu icon
            $( '#nav-main li a[href="' + hash + '"]' ).parents( 'li' ).addClass( 'active' );
        }

        // Add js class to body
        $( 'body' ).addClass( 'js' );

        // Load more blog articles
        $( '.blog-wrapper' ).append('<button class="load-more">Afficher plus d\'articles</button>');
        $( '.blog-wrapper article' ).eq(2).nextAll().hide();
        $( document ).on( 'click', '.blog-wrapper .load-more', function() {
            WAQ.Common.sequentialFadeIn( '.blog-wrapper article:hidden:lt(3)', 100, 'block', function(){
                if( $('.blog-wrapper article:hidden').length == 0 ){
                    $('.blog-wrapper .load-more').hide();
                }
            });
        });

        // Retour en haut du site
        $('.snapmenu-logo').on('click', function(e){
            var self = this,
                offset = 0;

            $('html, body').animate({
                scrollTop: offset
            }, function(){
                window.location.hash = '';
                $( '.home' ).find('h1').eq(0).attr('tabindex', '-1').focus();
            });

            e.preventDefault();
            return false;
        });

        // Hover sur les liens sociaux des comités
        $('.about .social-medias a').hover(
            function(){
                var url    = $(this).find('img').attr('src'),
                    newUrl = url.replace('-mono', '-mono-hover');
                $(this).find('img').attr('src', newUrl);
            },
            function(){
                var url    = $(this).find('img').attr('src'),
                    newUrl = url.replace('-mono-hover', '-mono');
                $(this).find('img').attr('src', newUrl);
            }
        );

    });

})(jQuery, window, document);