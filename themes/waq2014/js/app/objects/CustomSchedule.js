var CustomSchedule = (function($, window, document, undefined) {

    // Default configurations, can be overwritten when the object is created
    var defaultConfig = {};

    // Constructor
    function CustomSchedule(obj, config, app) {
        // Get reference to main Project object
        this.app = app;

        // Overwrite the default configuration
        this.config = $.extend({}, defaultConfig, config);

        // Set the "CustomSchedule" or the main container
        this.CustomSchedule = $(obj);

        // Is the user connected to facebook (null : not connected, true = connected, false = not authorized)
        this.facebookConnected = null;

        // Initialize default functions
        this.init();
    }

    CustomSchedule.prototype = {

        // Initialize default functions
        init: function() {
            var self = this;

            var fb_conf = {
                appId      : 'XXXXXXXXXXXXXXXX',                   // App ID from the app dashboard
                status     : true,                                 // Check Facebook Login status
                xfbml      : true,                                  // Look for social plugins on the page
                cookie : true
            }

            if(window.location.hostname == 'waq2014.dev.libeo.com'){
                fb_conf = {
                    appId      : '1421838541381572',                   // App ID from the app dashboard
                    status     : true,                                 // Check Facebook Login status
                    xfbml      : true,                                  // Look for social plugins on the page
                    cookie : true
                }
            }
            else if(window.location.hostname == 'waq2014.job.paulcote.net'){
                fb_conf = {
                    appId      : '1382147128676757',                   // App ID from the app dashboard
                    status     : true,                                 // Check Facebook Login status
                    xfbml      : true,                                  // Look for social plugins on the page
                    cookie : true
                }
            }



            if(typeof FB != undefined && fb_conf.appId != 'XXXXXXXXXXXXXXXX'){
                FB.init(fb_conf);

                FB.getLoginStatus(function(response) {
                    if (response.status === 'connected') {
                        self.facebookConnected = true;
                        $('.facebook-login').css('display','none');
                        $('.facebook-logout').css('display','block');
                        self.fbInitPhp(function(){self.getUserSessions();});
                    }
                    else{
                        $('.facebook-login').css('display','block');
                        $('.facebook-logout').css('display','none');
                        self.getUserSessions();
                        self.loadSessions();
                    }
                });
            }
            else{
              window.fbasyncinit = function(){
                FB.init(fb_conf);

                FB.getLoginStatus(function(response) {
                    if (response.status === 'connected') {
                        self.facebookConnected = true;
                        $('.facebook-login').css('display','none');
                        $('.facebook-logout').css('display','block');
                        self.fbInitPhp(function(){self.getUserSessions();});
                    }
                    else{
                        $('.facebook-login').css('display','block');
                        $('.facebook-logout').css('display','none');
                        self.getUserSessions();
                        self.loadSessions();
                    }
                });
              }
            }

            self.bindEvents();
        },

        bindEvents: function(){
            var self = this;

            $(document.body).on('click', '.session-bookmark.add', function(){

                if(self.facebookConnected == null){
                    self.facebookConnect('self.getUserSessions(\'self.addSession('+$(this).parents('td').attr("data-session-id")+')\')');
                }
                else{
                    self.addSession($(this).parents('td').attr("data-session-id"));
                }
            });

            $(document.body).on('click', '.session-bookmark.remove', function(){

                if(self.facebookConnected == null){
                    self.facebookConnect('self.getUserSessions(\'self.removeSession('+$(this).parents('td').attr("data-session-id")+')\')');
                }
                else{
                    self.removeSession($(this).parents('td').attr("data-session-id"));
                }
            });

            $(document.body).on('click', '.facebook-login', function(){
                self.facebookConnect(function(){self.getUserSessions(function(){self.saveUserSessions();});});
            });

            $(document.body).on('click', '.facebook-logout', function(){
                self.facebookConnected = null;
                FB.logout(function(response) {
                    $('.facebook-login').css('display','block');
                    $('.facebook-logout').css('display','none');
                });
            });

        },

        facebookConnect: function(callback){
            var self = this;

            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    self.facebookConnected = true;
                    $('.facebook-login').css('display','none');
                    $('.facebook-logout').css('display','block');
                    self.fbInitPhp(callback);
                }
                //else if (response.status === 'not_authorized') {
                //
                //}
                else {
                    FB.login(function(response) {
                        if (response.authResponse) {
                            self.facebookConnected = true;
                            $('.facebook-login').css('display','none');
                            $('.facebook-logout').css('display','block');
                            self.fbInitPhp(callback);
                        }
                        else {
                            self.facebookConnected = false;
                            if(typeof callback === 'function')
                                callback();
                            else if(typeof callback === 'string'){
                                var func = new Function(callback);
                                func();
                            }
                        }
                    });
                }
            });

        },

        addSession: function(session_id){
            var self = this;

            if(user_sessions.indexOf(parseInt(session_id)) == -1){
                user_sessions.push(parseInt(session_id));

                $('.session[data-session-id=\''+session_id+'\'] button').removeClass('add').addClass('remove');
                $('.session[data-session-id=\''+session_id+'\'] button').parents('td').addClass('bookmarked');
                $('.session[data-session-id=\''+session_id+'\'] button').find('span').text('Retirer cette conférence à mon horaire');
            }

            self.saveUserSessions();
            self.loadSessions();

        },

        removeSession: function(session_id){
            var self = this;

            if(user_sessions.indexOf(parseInt(session_id)) != -1){
                user_sessions.splice(user_sessions.indexOf(parseInt(session_id)), 1);

                $('.session[data-session-id=\''+session_id+'\'] button').removeClass('remove').addClass('add');
                $('.session[data-session-id=\''+session_id+'\']').removeClass('bookmarked');
                $('.session[data-session-id=\''+session_id+'\'] button').find('span').text('Ajouter cette conférence à mon horaire');
            }

            self.saveUserSessions();
            self.loadSessions();

        },

        fbInitPhp: function(callback){
            var self = this;
                
            if(typeof fb_init_ajax != 'undefined' && fb_init_ajax != ''){
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : fb_init_ajax,
                    success: function(response) {
                        if(typeof callback === 'function')
                            callback();
                        else if(typeof callback === 'string'){
                            var func = new Function(callback);
                            func();
                        }
                    }
                });
            }

        },

        saveUserSessions: function(){
            var self = this;

            if(self.facebookConnected && typeof save_user_sessions_ajax != 'undefined' && save_user_sessions_ajax != ''){
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : save_user_sessions_ajax,
                    data: {'user_sessions' : JSON.stringify(user_sessions)},
                    success: function(response) {
                    }
                });
            }
            else{
                self.createCookie('waq_user_sessions',JSON.stringify(user_sessions),365);
            }

        },

        getUserSessions: function(callback){
            var self = this;

            if(self.facebookConnected && typeof get_user_sessions_ajax != 'undefined' && get_user_sessions_ajax != ''){
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : get_user_sessions_ajax,
                    success: function(response) {
                        if(response.length > 0){
                            user_sessions =  user_sessions.concat(response).unique();
                        }

                        if(typeof callback === 'function')
                            callback();
                        else if(typeof callback === 'string'){
                            var func = new Function(callback);
                            func();
                        }

                        self.loadSessions();
                    }
                });
            }
            else{
                response = JSON.parse(self.readCookie('waq_user_sessions'));
                if(response != null && response.length > 0){
                    user_sessions =  user_sessions.concat(response).unique();
                }

                if(typeof callback === 'function')
                    callback();
                else if(typeof callback === 'string'){
                    var func = new Function(callback);
                    func();
                }
            }

        },

        loadSessions: function(){
            var self = this;

            $('.session button').removeClass('remove').addClass('add');
            $('.session button').find('span').text('Ajouter cette conférence à mon horaire');
            $('.session').removeClass('bookmarked');

            user_sessions.forEach(function(entry) {
                $('.session[data-session-id=\''+entry+'\'] button').removeClass('add').addClass('remove');
                $('.session[data-session-id=\''+entry+'\'] button').find('span').text('Retirer cette conférence à mon horaire');
                $('.session[data-session-id=\''+entry+'\']').addClass('bookmarked');
            });

        },

        createCookie: function(name,value,days) {
            var self = this;

            if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
            }
            else var expires = "";
            document.cookie = name+"="+value+expires+"; path=/";
        },

        readCookie: function(name) {
            var self = this;

            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        },

        eraseCookie: function(name) {
            var self = this;

            self.createCookie(name,"",-1);
        }

    };

    return CustomSchedule;

})(jQuery, window, document);

Array.prototype.unique = function() {
    var a = this.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
};