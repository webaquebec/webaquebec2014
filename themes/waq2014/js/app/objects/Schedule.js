var Schedule = ( function( $, window, document, undefined ) {

    // Default configurations, can be overwritten when the object is created
    var defaultConfig = {};

    // Constructor
    function Schedule( obj, config, app ) {
        // Get reference to main Project object
        this.app = app;

        // Overwrite the default configuration
        this.config = $.extend({
            changeSlideDuration : 300,
            filterSessionsDuration : 300,
        }, defaultConfig, config );

        // Set the "Schedule" or the main container
        this.schedule = $( obj );

        // Get slides wrapper
        this.wrapper = this.schedule.find( '.js-slider' );

        // Get slides container
        this.container = this.schedule.find( '.js-slider-container' );

        // Get all slides
        this.slides = this.schedule.find( '.slide' );

        // Get current slide
        this.currentSlide = ( this.slides.filter( '.active' ).length > 0 ? this.slides.filter( '.active' ) : this.slides.eq(0) ) ;

        // Get all slides number
        this.nbSlides = this.slides.length;

        // Get slides width
        this.slideWidth = this.slides.eq(0).outerWidth( true );

        // Get all sessions
        this.sessions = this.schedule.find('.session');

        // Get all buttons
        this.buttons = this.schedule.find( '.days-buttons button' );

        // Get all filter buttons
        this.filterButtons = this.schedule.find('.schedule-filters li > button');

        this.scrolling = false;

        // Initialize default functions
        this.init();
    }

    Schedule.prototype = {
        // Initialize function
        init: function() {
            var self = this;

            self.initLayout();
        },

        // Initialize layout
        initLayout: function() {
            var self = this,
                index = self.currentSlide.index(),
                offset = index / self.nbSlides * - 100;

            self.bindEvents();
            self.setHeight( self.currentSlide );

            self.slides.css('left', offset + '%' );
            self.buttons.removeClass( WAQ.Constants.isActiveClass );
            self.buttons.eq( index ).addClass( WAQ.Constants.isActiveClass );
            self.slides.filter(':not(.active)').find('a, button').attr('tabindex', '-1');
        },

        // Bind events
        bindEvents: function() {
            var self = this;

            self.buttons.on( 'click', function() {
                var $this = $( this ),
                    index = $this.index();

                self.scrolling = false;

                self.changeSlide( index );
            });

            self.filterButtons.on( 'click', function() {
                var $this = $( this ),
                    tag = $this.attr( 'data-slug' );

                self.filterButtons.removeClass( WAQ.Constants.isActiveClass );
                $this.addClass( WAQ.Constants.isActiveClass );

                self.filterSessions( tag );
            });

            self.schedule.find( '.btn-toggle-filters' ).on( 'click', function() {
                $(this).toggleClass( WAQ.Constants.isActiveClass );
                $(this).parents('.schedule-filters').find('ul').toggle();
            });

            self.sessions.hover(
                // Mouse in
                function(){
                    var $this = $( this ),
                        session = $this;

                    // Get class name that start with 'salle'
                    var roomClass = WAQ.Common.getClassStartingWith( session, 'salle-' );

                    // Extract room name from class name
                    var room = roomClass.replace( 'salle-', '' );

                    self.highlightSession( room );
                },
                // Mouse out
                function(){
                    self.highlightSession();
                }
            );

            $( window ).on( 'resize', function() {
                self.setLayout();
            });
        },

        // Set layout
        setLayout: function() {
            var self = this;

            self.setHeight( self.currentSlide );
        },

        // Set height of current slide
        setHeight: function( slide ) {
            var self = this,
                height = slide.outerHeight( true );

            self.wrapper.height( height );
            self.currentHeight = height;

            // Update navigation breakpoints
            if( ! $('html').hasClass('lt-ie9') ){
                SnapMenu.prototype.createBreakpoints.call( WAQ.SnapMenu[0] );
            }
        },

        // Change current slide
        changeSlide: function( index ) {
            var self = this,
                offset = index / self.nbSlides * - 100,
                currentSlide = self.slides.eq( index ),
                currentHeight = currentSlide.outerHeight( true );

            self.buttons.removeClass( WAQ.Constants.isActiveClass );
            self.buttons.eq( index ).addClass( WAQ.Constants.isActiveClass );

            if ( currentHeight > self.currentHeight ){
                self.setHeight( currentSlide );
            }

            self.slides.animate({
                left: offset + '%'
            }, self.config.changeSlideDuration, function(){
                self.currentIndex = index;
                self.currentSlide = currentSlide;
                self.setHeight( currentSlide );

                self.slides.removeClass( WAQ.Constants.isActiveClass );
                self.currentSlide.addClass( WAQ.Constants.isActiveClass );

                self.slides.filter('.' + WAQ.Constants.isActiveClass).find('a, button').removeAttr('tabindex');
                self.slides.filter(':not(.' + WAQ.Constants.isActiveClass + ')').find('a, button').attr('tabindex', '-1');

                // Check if current scroll position is inside schedule. If not, scroll back to schedule.
                if ( $( window ).scrollTop() + 200 > self.wrapper.offset().top + self.wrapper.height() ){

                    if( ! self.scrolling ){
                        $('html, body').animate({
                            scrollTop: self.wrapper.offset().top - 133
                        }, 0);

                        self.scrolling = true;
                    }
                }
            });
        },

        // Filter sessions
        filterSessions: function( tag ){
            var self = this,
                filteredSessions = self.sessions.filter( '.' + tag + ':not(.break)' ),
                unfilteredSessions = self.sessions.filter( ':not(.' + tag + '):not(.break)' );

            unfilteredSessions.removeClass('filtered');
            filteredSessions.addClass('filtered');

            filteredSessions.animate({
                opacity: 1
            }, self.config.filterSessionsDuration);

            unfilteredSessions.animate({
                opacity: 0.3
            }, self.config.filterSessionsDuration);
        },

        //
        highlightSession: function( room ) {
            // Get wrapper element
            var $pageWrapper = $( '.l-page-wrapper' );

            // Get current highlight class
            var currentClass = WAQ.Common.getClassStartingWith( $pageWrapper, 'highlight-' );

            // Remove current highlight class and add new highlight class
            $pageWrapper.removeClass( currentClass );

            if( typeof( room ) !== 'undefined' ){
                $pageWrapper.addClass( 'highlight-' + room );
            }
        }
    };

    return Schedule;

})( jQuery, window, document );