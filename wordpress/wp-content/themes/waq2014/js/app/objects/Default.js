var Slideshow = (function($, window, document, undefined) {

    // Default configurations, can be overwritten when the object is created
    var defaultConfig = {};

    // Constructor
    function Slideshow(obj, config, app) {
        // Get reference to main Project object
        this.app = app;

        // Overwrite the default configuration
        this.config = $.extend({}, defaultConfig, config);

        // Set the "slideshow" or the main container
        this.slideshow = $(obj);

        // Initialize default functions
        this.init();
    }

    Slideshow.prototype = {
        // Check if a fold has content or if it's empty. If empty, replace the button by a span
        init: function() {
            var self = this;
        }
    };

    return Slideshow;

})(jQuery, window, document);