var MobileMenu = ( function( $, window, document, undefined ) {

    // Default configurations, can be overwritten when the object is created
    var defaultConfig = {};

    // Constructor
    function MobileMenu( obj, config, app ) {
        // Get reference to main Project object
        this.app = app;

        // Overwrite the default configuration
        this.config = $.extend({}, defaultConfig, config );

        // Set the "MobileMenu" or the main container
        this.mobilemenu = $( obj );

        // Initialize default functions
        this.init();
    }

    MobileMenu.prototype = {
        // Initialize function
        init: function() {
            var self = this;

            self.bindEvents();
        },

        // Bind events
        bindEvents: function() {
            var self = this,
                opened = false;

            // Toggle mobile menu
            $( document ).on( 'click', '.btn-toggle-menu', function() {
                if( opened ){
                    self.closeMenu();
                    opened = false;
                }
                else {
                    self.openMenu();
                    opened = true;
                }
            });

            $( '.snapmenu-mobile-buttons a' ).on('click', function( e ) {
                self.closeMenu();
                opened = false;
                window.location.hash = '#horaire';
                $( window ).scrollTop( $( '#horaire' ).offset().top - 64 + 15);
                e.preventDefault();
            });

            // Close menu
            $( '.nav-main-wrapper li a' ).on('click', function() {
                self.closeMenu();
                opened = false;
            });

            $( window ).resize( function() {
                if( $( window ).width() < 1167 ){
                    self.adjustMenuHeight();
                }
                else {
                    opened = false;
                    self.mobilemenu.removeClass( WAQ.Constants.isOpenClass );
                }
            });
        },

        // Open menu event
        openMenu: function() {
            var self = this;

            //if( $( window ).width() < 1167 ){

                self.adjustMenuHeight();

                self.mobilemenu.addClass( WAQ.Constants.isOpenClass );

                $( '#nav-main li' ).hide();
                WAQ.Common.sequentialFadeIn( '#nav-main li', 100, 'block');

            //}

        },

        // Close menu event
        closeMenu: function() {
            var self = this;

            //if( $( window ).width() < 1167 ){

                WAQ.Common.sequentialFadeOut( '#nav-main li', 100, 'block', function(){
                    self.mobilemenu.removeClass( WAQ.Constants.isOpenClass );
                });

            //}

        },

        adjustMenuHeight: function() {
            var self = this,
                windowHeight = $( window ).height();
                menuElements = self.mobilemenu.find( 'li' ),
                menuElementsHeight = Math.ceil( ( windowHeight - 66 ) / menuElements.length );

            //if( $( window ).width() < 1167 ){

                menuElements.height( menuElementsHeight );
                menuElements.find( 'a' ).css( 'line-height', menuElementsHeight + 'px' );
                menuElements.find( 'a' ).css( 'font-size', Math.ceil( menuElementsHeight / 5 + 10 ) + 'px' );

            //}
        }
    };

    return MobileMenu;

})( jQuery, window, document );