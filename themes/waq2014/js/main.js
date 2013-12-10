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
            WAQ.Schedule = WAQ.Common.createObjects($('.schedule'), Schedule);
            WAQ.CustomSchedule = WAQ.Common.createObjects($('.schedule-wrapper'), CustomSchedule);
            WAQ.Countdown = WAQ.Common.createObjects($('.event-stats'), Countdown);

            // Common functions
            WAQ.Common.goToContent($('.l-a11y'));
            WAQ.Common.betterFocus(['a', 'button', 'input']);
            WAQ.Common.checkForKeyboardInput();
            WAQ.Common.checkFontSize();
            WAQ.Common.externalLinks();
        }

    });

})(jQuery, window, document);