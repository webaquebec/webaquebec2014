var Countdown = (function($, window, document, undefined) {

    // Default configurations, can be overwritten when the object is created
    var defaultConfig = {};

    // Constructor
    function Countdown(obj, config, app) {
        // Get reference to main Project object
        this.app = app;

        // Overwrite the default configuration
        this.config = $.extend({}, defaultConfig, config);
        
        // Stats
        this.stats = $( obj );

        // Set the "Countdown" or the main container
        this.Countdown = $(obj);
        
        // End date of countdown
        this.endDateTime = null

        // Initialize default functions
        this.init();
    }

    Countdown.prototype = {
        // Initialize default functions
        init: function() {
            var self = this;
            
            self.endDateTime = parseInt($('.event-stats-group.hours').attr('data-from-unix-time'));
            
            setInterval(function(){self.update()}, 1000);
        },
        
        update: function(){
            var self = this;
            
            var currentTime = Math.round(new Date().getTime() / 1000);
            
            var diff = self.endDateTime-currentTime;
            
            var days = Math.floor(diff/(60*60*24));
            diff = diff-(days*(60*60*24));
            var hours = Math.floor(diff/(60*60));
            diff = diff-(hours*(60*60));
            var minutes = Math.floor(diff/60);
            diff = diff-(minutes*60);
            var secs = diff;
            
            if(days < 1){
              hours = hours + (days*24);
              self.stats.find('.days').addClass('visuallyhidden');
              self.stats.find('.seconds').removeClass('visuallyhidden');
            }
            
            self.stats.find('.days .stat-number').text(days);
            if(days == 1){self.stats.find('.days .stat-caption').text('jour');}else{self.stats.find('.days .stat-caption').text('jours');}
            self.stats.find('.hours .stat-number').text(hours);
            if(hours == 1){self.stats.find('.hours .stat-caption').text('heure');}else{self.stats.find('.hours .stat-caption').text('heures');}
            self.stats.find('.minutes .stat-number').text(minutes);
            if(minutes == 1){self.stats.find('.minutes .stat-caption').text('minute');}else{self.stats.find('.minutes .stat-caption').text('minutes');}
            self.stats.find('.seconds .stat-number').text(secs);
            if(secs == 1){self.stats.find('.seconds .stat-caption').text('seconde');}else{self.stats.find('.seconds .stat-caption').text('secondes');}
        }
    };

    return Countdown;

})(jQuery, window, document);