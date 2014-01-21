var SnapMenu = ( function( $, window, document, undefined ) {

    // Default configurations, can be overwritten when the object is created
    var defaultConfig = {
        adjustTopOffset: 0,
        adjustBottomOffset: 0,
        delimiterElement: $('body'),
        customNavigation: false,
        breakpointsSelector: '',
    };

    // Constructor
    function SnapMenu( obj, config, app ) {
        // Get reference to main Project object
        this.app = app;

        // Overwrite the default configuration
        this.config = $.extend({}, defaultConfig, config );

        // Set the "SnapMenu" or the main container
        this.snapmenu = $( obj );

        // Get menu items
        this.menuItems = this.snapmenu.find('li a');

        // Get delimiter element start and end position
        this.delimiterTop = this.config.delimiterElement.offset().top + this.config.adjustTopOffset;
        this.delimiterBottom = this.config.delimiterElement.offset().top + this.config.delimiterElement.height() + this.config.adjustBottomOffset;

        // Initialize breakpoints
        this.breakpoints = new Array();

        // Initialize default functions
        this.init();
    }

    SnapMenu.prototype = {
        // Initialize function
        init: function() {
            var self = this;

            if( $( window ).width() < 1167 ){
                self.snap( true );
            }

            self.bindEvents();
            self.createBreakpoints();
        },

        // Bind events
        bindEvents: function() {
            var self = this;

            $( window ).scroll( function() {
                if( $( window ).width() < 1167 ){
                    self.snap( true );
                } else {
                    self.snap();
                }
                self.checkCurrentBreakpoint( $(window).scrollTop() );
            });

            $( window ).resize( function() {
                if( $( window ).width() < 1167 && self.config.customNavigation ){
                    self.snap( true );
                } else if ( $( window ).width() < 1167 ){
                    $('.days-buttons.isSnap').removeClass('isSnap');
                }
            });

            self.menuItems.on( 'click', function( e ) {
                var $this = $(this);

                self.menuItems.parent().removeClass( WAQ.Constants.isActiveClass );
                $this.parent().addClass( WAQ.Constants.isActiveClass );

                self.customNavigation( $(this).attr('href') );

                e.preventDefault();
                return false;
            });

            self.menuItems.on( 'mousedown', function( e ) {
                $( $(this).attr('href') ).find('h2').eq(0).addClass( WAQ.Constants.onClickClass );

                e.preventDefault();
                return false;
            });
        },

        // Snap event
        snap: function( forceSnap ) {
            var self = this;

            // Update delimiter element start and end position
            self.delimiterTop = self.config.delimiterElement.offset().top + self.config.adjustTopOffset;
            self.delimiterBottom = self.config.delimiterElement.offset().top + self.config.delimiterElement.height() + self.config.adjustBottomOffset;

            if ( self.delimiterTop < $( window ).scrollTop() && $( window ).scrollTop() < self.delimiterBottom || forceSnap ) {
                self.snapmenu.addClass('isSnap');
            }

            else {
                self.snapmenu.removeClass('isSnap');
            }
        },

        // Custom navigation
        customNavigation: function( anchor ) {
            var self = this,
                offset = $( anchor ).offset().top - 64 + 15;

            if ( self.config.customNavigation ) {
                //$( window ).scrollTop( $( anchor ).offset().top - 64 + 15 );
                $('html, body').animate({
                    scrollTop: offset
                }, function(){
                    window.location.hash = anchor;
                    $( anchor ).find('h2').eq(0).attr('tabindex', '-1').focus();
                });

            }
        },

        createBreakpoints: function() {
            var self = this;

            if ( self.config.customNavigation ) {

                $( self.config.breakpointsSelector ).each( function() {
                    var $this = $(this);

                    self.breakpoints[ $this.attr('id') ] = $this.offset().top;
                });

            }
        },

        checkCurrentBreakpoint: function( offset ) {
            var self = this,
                breakpoints = self.breakpoints,
                offset = offset + 100;

            if ( self.config.customNavigation ) {

                if ( breakpoints['a-propos'] <= offset ){
                    self.menuItems.parent().removeClass( WAQ.Constants.isActiveClass );
                    self.menuItems.parent().find('a[href="#a-propos"]').parents('li').addClass( WAQ.Constants.isActiveClass );
                }

                else if ( breakpoints['a-propos'] >= offset && offset >= breakpoints['partenaires'] ){
                    self.menuItems.parent().removeClass( WAQ.Constants.isActiveClass );
                    self.menuItems.parent().find('a[href="#partenaires"]').parents('li').addClass( WAQ.Constants.isActiveClass );
                }

                else if ( breakpoints['partenaires'] >= offset && offset >= breakpoints['blogue'] ){
                    self.menuItems.parent().removeClass( WAQ.Constants.isActiveClass );
                    self.menuItems.parent().find('a[href="#blogue"]').parents('li').addClass( WAQ.Constants.isActiveClass );
                }

                else if ( breakpoints['blogue'] >= offset && offset >= breakpoints['lieu-et-coordonnees'] ){
                    self.menuItems.parent().removeClass( WAQ.Constants.isActiveClass );
                    self.menuItems.parent().find('a[href="#lieu-et-coordonnees"]').parents('li').addClass( WAQ.Constants.isActiveClass );
                }

                else if ( breakpoints['lieu-et-coordonnees'] >= offset && offset >= breakpoints['horaire'] ){
                    self.menuItems.parent().removeClass( WAQ.Constants.isActiveClass );
                    self.menuItems.parent().find('a[href="#horaire"]').parents('li').addClass( WAQ.Constants.isActiveClass );
                }

                else if ( breakpoints['horaire'] >= offset ){
                    self.menuItems.parent().removeClass( WAQ.Constants.isActiveClass );
                }

            }
        }
    };

    return SnapMenu;

})( jQuery, window, document );