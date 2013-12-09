$(window).load(function(){

	$('.days-buttons button').eq(0).addClass('active');

	$('.days-buttons button').on('click', function(){

		$('.days-buttons button').removeClass('active');
		$(this).addClass('active');

		var index = $(this).index(),
			width = $('.slide').eq(0).outerWidth(true);

		$('.schedule .slide').animate({
			left: index * width * -1,
		}, 300);

		$('.schedule .slide').eq(0).find('th').animate({
			left: index * width,
		}, 300);

	});

});


var facebook_connected = null;
// Facebook
$(window).load(function(){

	$(document.body).on('click', '.session-bookmark.add', function(){

	    if(facebook_connected == null){
	      facebook_connect('get_user_sessions(\'add_session('+$(this).parents('td').attr("data-session-id")+')\')');
	    }
	    else{
	      add_session($(this).parents('td').attr("data-session-id"));
	    }
	});

	$(document.body).on('click', '.session-bookmark.remove', function(){

	    if(facebook_connected == null){
	      facebook_connect('get_user_sessions(\'remove_session('+$(this).parents('td').attr("data-session-id")+')\')');
	    }
	    else{
	      remove_session($(this).parents('td').attr("data-session-id"));
	    }
	});

	$(document.body).on('click', '.facebook-connect', function(){
	  facebook_connect(function(){get_user_sessions(function(){save_user_sessions();});});
	});

	$(document.body).on('click', '.facebook-logout', function(){
	  facebook_connected = null;
	  FB.logout(function(response) {
	    $('.facebook-connect').css('display','block');
	    $('.facebook-logout').css('display','none');
	  });
	});

});

function add_session(session_id){
  if(user_sessions.indexOf(parseInt(session_id)) == -1){
    user_sessions.push(parseInt(session_id));

    $('.session[data-session-id=\''+session_id+'\'] button').removeClass('add').addClass('remove');
    $('.session[data-session-id=\''+session_id+'\'] button').parents('td').addClass('bookmarked');
    $('.session[data-session-id=\''+session_id+'\'] button').find('span').text('Retirer cette conférence à mon horaire');
  }

  save_user_sessions();
  load_sessions();
}

function remove_session(session_id){
  if(user_sessions.indexOf(parseInt(session_id)) != -1){
    user_sessions.splice(user_sessions.indexOf(parseInt(session_id)), 1);

    $('.session[data-session-id=\''+session_id+'\'] button').removeClass('remove').addClass('add');
    $('.session[data-session-id=\''+session_id+'\']').removeClass('bookmarked');
    $('.session[data-session-id=\''+session_id+'\'] button').find('span').text('Ajouter cette conférence à mon horaire');
  }

  save_user_sessions();
  load_sessions();
}

function facebook_connect(callback){

    FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        facebook_connected = true;
        $('.facebook-connect').css('display','none');
        $('.facebook-logout').css('display','block');
        fb_init_php(callback);
      }
      //else if (response.status === 'not_authorized') {
      //
      //}
      else {
        FB.login(function(response) {
          if (response.authResponse) {
            facebook_connected = true;
            $('.facebook-connect').css('display','none');
            $('.facebook-logout').css('display','block');
            fb_init_php(callback);
          }
          else {
            facebook_connected = false;
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

}

function fb_init_php(callback){
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

function save_user_sessions(){

  if(facebook_connected){
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
    createCookie('waq_user_sessions',JSON.stringify(user_sessions),365);
  }

}

function get_user_sessions(callback){

  if(facebook_connected){
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

         load_sessions();
       }
    });
  }
  else{
    response = JSON.parse(readCookie('waq_user_sessions'));
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

}

function load_sessions(){

   $('.session button').removeClass('remove').addClass('add');
   $('.session button').find('span').text('Ajouter cette conférence à mon horaire');
   $('.session').removeClass('bookmarked');

   user_sessions.forEach(function(entry) {
       $('.session[data-session-id=\''+entry+'\'] button').removeClass('add').addClass('remove');
       $('.session[data-session-id=\''+entry+'\'] button').find('span').text('Retirer cette conférence à mon horaire');
       $('.session[data-session-id=\''+entry+'\']').addClass('bookmarked');
   });

}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

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