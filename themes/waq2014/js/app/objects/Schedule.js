var Schedule = ( function( $, window, document, undefined ) {

    // Default configurations, can be overwritten when the object is created
    var defaultConfig = {};

    // Constructor
    function Schedule( obj, config, app ) {
        // Get reference to main Project object
        this.app = app;

        // Overwrite the default configuration
        this.config = $.extend({
            changeSlideDuration : 300
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

        // Get all buttons
        this.buttons = this.schedule.find( '.days-buttons button' );

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
            self.buttons.removeClass( 'active' );
            self.buttons.eq( index ).addClass( 'active' );
        },

        // Bind events
        bindEvents: function() {
            var self = this;

            self.buttons.on( 'click', function() {
                var $this = $(this),
                    index = $this.index();

                self.changeSlide( index );
            });

            $( window ).on( 'resize', function() {
                //self.setLayout();
            });
        },

        // Set layout
        setLayout: function() {
            var self = this,
                index = self.currentSlide.index();

            self.slideWidth = self.currentSlide.outerWidth( true );

            self.setHeight( self.currentSlide );
            self.slides.css('left', index / self.nbSlides * - 100 );
        },

        // Set height of current slide
        setHeight: function( slide ) {
            var self = this,
                height = slide.outerHeight( true );

            self.wrapper.height( height );
            self.currentHeight = height;
        },

        // Change current slide
        changeSlide: function( index ) {
            var self = this,
                offset = index / self.nbSlides * - 100,
                currentSlide = self.slides.eq( index ),
                currentHeight = currentSlide.outerHeight( true );

            self.buttons.removeClass( 'active' );
            self.buttons.eq( index ).addClass( 'active' );

            if ( currentHeight > self.currentHeight ){
                self.setHeight( currentSlide );
            }

            self.slides.animate({
                left: offset + '%'
            }, self.config.changeSlideDuration, function(){
                self.currentIndex = index;
                self.currentSlide = currentSlide;
                self.setHeight( currentSlide );
            });
        }
    };

    return Schedule;

})( jQuery, window, document );