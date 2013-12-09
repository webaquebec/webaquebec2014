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
        this.currentSlide = ( this.slides.filter( '.active' ).length > 1 ? this.slides.filter( '.active' ) : this.slides.eq(0) ) ;

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
        // Check if a fold has content or if it's empty. If empty, replace the button by a span
        init: function() {
            var self = this;

            self.initLayout();
        },

        // Initiate layout
        initLayout: function() {
            var self = this;

            self.bindEvents();
            self.setHeight( self.currentSlide );
            self.changeSlide( self.currentSlide.index() );
        },

        // Bind events
        bindEvents: function() {
            var self = this;

            self.buttons.on('click', function(){
                var $this = $(this),
                    index = $this.index();

                self.changeSlide( index, function(){
                    console.log('callback called');
                });
            });
        },

        // Set height of current slide
        setHeight: function( slide ) {
            var self = this,
                height = slide.outerHeight( true );

            self.wrapper.height( height );
        },

        // Change slides
        changeSlide: function( index, callback ) {
            var self = this,
                offset = self.slideWidth * ( index * -1 )/*,
                callback = ( callback ? callback() : function(){} )*/;

            if( callback ){
                callback();
            }

            self.buttons.removeClass( 'active' );
            self.buttons.eq( index ).addClass( 'active' );

            self.slides.animate({
                left: offset
            }, self.config.changeSlideDuration );
        }
    };

    return Schedule;

})( jQuery, window, document );