jQuery(function () {
  checkFontSize();
  jQuery(document).bind('fontresize', function() {
    checkFontSize();
  });

  jQuery('a, button').on('mousedown', function(){
    $('.click').removeClass('click');
    $(this).addClass('click');
  });

  jQuery('.nav-main a, .nav-news a').on('focus',function(){
    $(this).parents('li').addClass('focus');
  });

  jQuery('.nav-main a, .nav-news a').on('blur',function(){
    $(this).parents('li').removeClass('focus');
  });

  jQuery('#loginform input[type="text"]').attr('placeholder', 'Adresse courriel');
  jQuery('#loginform input[type="password"]').attr('placeholder', 'Mot de passe');

  jQuery('#registerform input#user_email').attr('placeholder', 'Adresse courriel');
  jQuery('#registerform input#user_pass').attr('placeholder', 'Mot de passe');
  jQuery('#registerform input#user_pass_confirm').attr('placeholder', 'Confirmation du mot de passe');

  if( !jQuery('#your-profile .page-content').hasClass('showFields') ){
    jQuery('#your-profile .infoperso .editinfo').hide();
    jQuery('#your-profile .infoperso .displayinfo').show();
  }

  jQuery(document).on('click', '#your-profile .edit', function(){

    // Backup defaults values
    jQuery('.defaults').html('');
    jQuery( jQuery(this).parents('.edit-section').html() ).clone().appendTo('.defaults');


  	jQuery('.edit-section.active').removeClass('.active');
    jQuery('.edit-section.active').find('.displayinfo').show();
    jQuery('.edit-section.active').find('.editinfo').hide();
  	jQuery(this).parents('.edit-section').addClass('active');
    jQuery(this).parents('.edit-section').find('.displayinfo').toggle();
    jQuery(this).parents('.edit-section').find('.editinfo').toggle();

  	val = '';
  	if     ( jQuery(this).parents('.edit-section').hasClass('infoperso') )  val = 'infoperso';
  	else if( jQuery(this).parents('.edit-section').hasClass('coordonnees') ) val = 'coordonnees';
  	else if( jQuery(this).parents('.edit-section').hasClass('mycauses') ) 	val = 'mycauses';
  	else if( jQuery(this).parents('.edit-section').hasClass('mynews') ) 		val = 'mynews';
  	else if( jQuery(this).parents('.edit-section').hasClass('myrss') ) 		  val = 'myrss';
		jQuery('#editedsection').val( val );

    jQuery('#your-profile .close').replaceWith('<a href="#" class="edit"><span class="visuallyhidden">Éditer cette section</span></a>');
  	jQuery(this).replaceWith('<a href="#" class="close"><span class="visuallyhidden">Fermer cette section</span></a>');

    return false;
  });

  jQuery(document).on('click', '#your-profile .close', function(){
  	jQuery('.edit-section').removeClass('.active');
    jQuery('.edit-section').find('.displayinfo').show();
    jQuery('.edit-section').find('.editinfo').hide();

    // Restore default values
    jQuery(this).parents('.edit-section').html( jQuery('.defaults').html() );
    jQuery('.defaults').html('');

  	jQuery(this).replaceWith('<a href="#" class="edit"><span class="visuallyhidden">Éditer cette section</span></a>');
    return false;
  });

  jQuery('.datepicker-icon').on('click', function(e){
    jQuery(this).prev('input').focus();
    return false;
  });

  jQuery(document).on('keydown', '.filterbox', function(e){
    if( e.keyCode == 13 ){
      jQuery('input').blur();
      jQuery('form').submit();
    }
  });

  jQuery('.support-button').on('click', function(e){
    jQuery.ajax({
      type: 'POST',
      dataType: 'html',
      url: ajaxurl,
      data: {
        'action': 'newappuis', //calls wp_ajax_nopriv_ajaxlogin
        'id' : post_id
      },
      success: function(data){
        var nbAppuis = parseInt( jQuery('.support-total strong').html() );
        jQuery('.support-button').replaceWith('<span class="support-voted">Vous avez appuyé cette cause</span>');
        jQuery('.support-total strong').html(nbAppuis + 1);
        jQuery('.appuis p span').html(nbAppuis + 1);
      }
    });
    e.preventDefault();
  });

  jQuery('.user-flag').on('click', function(e){
    $.ajax({
      type: 'POST',
      dataType: 'html',
      url: ajaxurl,
      data: {
        'action': 'newsignalement', //calls wp_ajax_nopriv_ajaxlogin
        'post_id' : jQuery('.postid').html()
      },
      success: function(data){
        jQuery('.user-flag.active').replaceWith('<p class="user-flag light"><span>Vous avez signalé cette publication.</span></p>');
      }
    });
    e.preventDefault();
  });

  jQuery('.galerie-list .photo, .galerie-list .video').on('click', function(e){

    if(!jQuery(this).hasClass('viewed')){
      jQuery(this).addClass('viewed');
      $.ajax({
        type: 'POST',
        dataType: 'html',
        url: ajaxurl,
        data: {
          'action': 'newgalleryview', //calls wp_ajax_nopriv_ajaxlogin
          'media_meta_id' : jQuery(this).attr('data-media-meta-id'),
          'media_blog_id' : jQuery(this).attr('data-media-blog-id')
        },
        success: function(data){
          console.log(data);
        }
      });
    }
    e.preventDefault();
  });

  jQuery('.widget.galerie.top a').on('click', function(e){

    if(jQuery(this).parents('.photo, .video').hasClass('video')){
      if(jQuery('.galerie-slideshow .slide-active').find('iframe').length > 0){
        jQuery('.galerie-slideshow .slide-active').find('iframe').attr('src','//www.youtube.com/embed/'+jQuery(this).parents('.photo, .video').attr('data-yt-video-id'));
      }
      else{
        jQuery('.galerie-slideshow .slide-active').find('figure').before('<iframe width="589" height="331" src="//www.youtube.com/embed/'+jQuery(this).parents('.photo, .video').attr('data-yt-video-id')+'" frameborder="0" allowfullscreen></iframe>');
      }
      jQuery('.galerie-slideshow .slide-active').find('.img-crop').css('display','none');
      jQuery('.galerie-slideshow .slide-active').find('iframe').css('display','block');
    }
    else{
      jQuery('.galerie-slideshow .slide-active').find('.img-crop').css('display','block');
      jQuery('.galerie-slideshow .slide-active').find('iframe').remove();
    	jQuery('.galerie-slideshow .slide-active').find('img').attr('src', jQuery(this).parents('.photo').find('img').attr('src'));
    }
  	jQuery('.galerie-slideshow .slide-active').find('h2').html(jQuery(this).parents('.photo,.video').find('h3 a').html());
  	jQuery('.galerie-slideshow .slide-active').find('p').html(jQuery(this).parents('.photo,.video').find('p.visuallyhidden a').html());
    jQuery('.galerie-slideshow .slide-active').find('h2').wrapInner( "<a href='#'></a>" ).find('a').attr('href', jQuery(this).parents('.photo,.video').find('p.visuallyhidden a').attr('href'));

    jQuery('.galerie-slideshow .buttons button').css('display','none');
    jQuery('.galerie-list ul li').removeClass('active');
    mediaAddView(jQuery(this).parents('.photo, .video'));
    changeShareBoxesUrlMedia(jQuery(this).parents('.photo, .video').attr('data-media-meta-id'), jQuery(this).parents('.photo, .video').attr('data-media-blog-id'));
    e.preventDefault();
  });

  jQuery('.fancybox').fancybox({
  	padding : 30,
    margin  : 0,
    maxWidth : 920,
    openEffect  : 'none',
    closeEffect : 'none',
    prevEffect  : 'none',
    nextEffect : 'none',
    helpers : {
      title : {
				type : 'inside'
			},
      overlay: {
        locked: true
      }
    },
    beforeShow : function() {
			this.title = '<h1>' + this.title + '</h1>'
			    			 + ( this.index + 1 <= this.group.length ? '<p class="nbslides">' + (this.index + 1) + ' de ' + this.group.length + '' + '</p>' : '' )
			    			 + '<p>' + jQuery(document).find('.fancybox').eq(this.index).find('img').attr('alt') + '</p>';
		}
  });

  jQuery('.fancybox-map').fancybox({
    padding : 30,
    margin  : 0,
    width : 800,
    height : 600,
    maxWidth : 800,
    minHeight : 600,
    openEffect  : 'none',
    closeEffect : 'none',
    prevEffect  : 'none',
    nextEffect : 'none'
  });

  jQuery('.fancybox-usersubmit').fancybox({
    width : 830,
    height : '90%',
    minHeight : '90%',
    maxWidth : 830,
    openEffect  : 'fade',
    closeEffect : 'fade',
    type : 'iframe',
    iframe : {
      scrolling : 'auto'
    },
    preload : true,
    helpers : {
      title : null
    }
  });

  jQuery('.fancybox-login a').fancybox({
    width : 800,
    height : 530,
    minHeight : 530,
    maxWidth : 800,
    openEffect  : 'fade',
    closeEffect : 'fade',
    type : 'iframe',
    helpers : {
      title : null
    }
  });

  jQuery(document).on('click', '.fancybox-overlay', function(e) {
    if( e.target !== jQuery('.fancybox-overlay').get(0) )
           return;
    $.fancybox.close();
  });

});

jQuery(window).load(function(){

  if( getURLParameter('registered') == 'true' ){
  	jQuery('.fancybox-login').click();
  }

  if( getURLParameter('action') == 'appui' ){
  	jQuery('.support-button').click();
  }

  sliderHomepage();
  sliderGallerie();
  sliderInline();
  regionsDropdown();
  sliderPartners();
  sliderPartnersHomepage();
  sliderMicrosite();
  toggleFilterBox();
  tabs();
  eventsTabs();
  eventsRegionsDropdown();
  multisiteDropdown();
  jQuery( '.datepicker' ).datepicker({
    dateFormat: "yy-mm-dd",
    prevText: "<",
    nextText: ">",
    dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
    dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
    monthNames: [ "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ]
  });
  console.log( jQuery( '.datepicker' ) );
});

function checkFontSize(){
  var fontsize = parseInt(jQuery('body').css('font-size').replace('px',''));
  if(fontsize > 16){ jQuery('body').addClass('zoom'); }
  else { jQuery('body').removeClass('zoom'); }
}//checkFontSize();

function sliderHomepage(){
  var $wrapper = jQuery('.homepage-slideshow');
  var $container = $wrapper.find('.slider-container');
  var $slides = $wrapper.find('.slide');
  var $buttons = $wrapper.find('.slider-buttons button:not(active)');
  var $activeSlide = $slides.eq(0);
  var activeSlidePos = $activeSlide.index();
  var nbSlides = $slides.length;
  var slideWidth = $slides.eq(0).outerWidth(true);
  var $nextSlide;
  var anim = true;

  $slides.not($activeSlide).hide();
  $buttons.eq(activeSlidePos).addClass('active');
  $activeSlide.addClass('active');
  $container.width(slideWidth * nbSlides);

  $buttons.on('click', function(){
    $this = jQuery(this);

    if(anim && !$this.hasClass('active')){
      index = $this.index();
      activeSlidePos = index;
      $nextSlide = $slides.eq(activeSlidePos);

      anim = false;
      $buttons.removeClass('active').eq(activeSlidePos).addClass('active');
      $slides.removeClass('nextSlide').eq(activeSlidePos).addClass('nextSlide');
      $nextSlide.show().css('left', slideWidth);

      //Animations
      $activeSlide.find('h2').fadeOut(300, function(){
        $slides.find('h2').hide();
        $slides.animate({
          left: "+=" + slideWidth * -1
        }, 500).promise().done(function(){
          $activeSlide.removeClass('active').hide();
          $slides.css('left', 0);
          $activeSlide = $nextSlide;
          $activeSlide.find('h2').fadeIn(300);
          anim = true;
        });
      });
    }// endif
  });
}//sliderHomepage();

function sliderPartners(){
  var $wrapper = jQuery('.partners-slideshow:not(.homepage)');
  var $container = $wrapper.find('.slider-container');
  var $slides = $wrapper.find('.slide');
  var $buttons = $wrapper.find('.buttons button');
  var slideWidth = $slides.eq(0).outerWidth(true);
  var nbSlides = $slides.length;
  var activeSlidePos = 0;
  var anim = true;
  var prev = false;

  $container.width(slideWidth * nbSlides);

  $buttons.eq(0).hide();

  if( nbSlides <= 3 ){
  	$buttons.hide();
  }

  $buttons.on('click', function(){
    prev = (jQuery(this).hasClass('prev') ? true : false);
    $buttons.show();
    if(prev && activeSlidePos == 0){
    	$buttons.eq(0).hide();
    	return false
    };
    if(!prev && activeSlidePos == nbSlides - 3){
    	$buttons.eq(1).hide();
    	return false
    };
    if(anim){
      anim = false;
      $slides.animate({
        left: '+=' + slideWidth * (prev ? 1 : -1)
      }, 500).promise().done(function(){
        anim = true;
    		activeSlidePos = (prev ? activeSlidePos - 1 : activeSlidePos + 1);
		    if(prev && activeSlidePos == 0){
		    	$buttons.eq(0).hide();
		    };
		    if(!prev && activeSlidePos == nbSlides - 3){
		    	$buttons.eq(1).hide();
		    };
      });
    }
  });
}//sliderPartners();

function sliderPartnersHomepage(){
  var $wrapper = jQuery('.partners-slideshow.homepage');
  var $container = $wrapper.find('.slider-container');
  var $slides = $wrapper.find('.slide');
  var $button = $wrapper.find('.next');
  var slideWidth = $slides.eq(0).outerWidth(true);
  var nbSlides = $slides.length;

  var anim = true;
  $container.width(slideWidth * nbSlides);

  $button.on('click', function(){
    if(anim){
      anim = false;
      console.log('anim');
      $slides.animate({
        left: '+=' + slideWidth * -1
      }, 500).promise().done(function(){
        $slides = $wrapper.find('.slide');
        $slides.eq(0).appendTo($container);
        $slides.css('left', 0);
        anim = true;
      });
    }
  });

}//sliderPartnersHomepage();

function sliderGallerie(){
  var $wrapper = jQuery('.galerie-slideshow');
  var $slideActive = $wrapper.find('.slide-active');
  var $buttons = $wrapper.find('.buttons button');
  var $slides = jQuery('.galerie-list ul li');
  var nbSlides = $slides.length;
  var activeSlidePos = 0;

  $slides.eq(0).addClass('active');
  $buttons.eq(0).hide();

  if( nbSlides <= 1 ){
    $buttons.eq(1).hide();
  }

	for(var i = 0; i < nbSlides; i += 3) {
	  equalHeights( $slides.slice(i, i + 3) );
	}

  $buttons.eq(0).on('click', function(){
  	if(activeSlidePos == 0){
  		$buttons.eq(0).hide();
  	} else {
  		$buttons.eq(0).show();
  		activeSlidePos--;
  		if($slides.eq(activeSlidePos).hasClass('video')){
  		  if($slideActive.find('iframe').length > 0){
  		    $slideActive.find('iframe').attr('src','//www.youtube.com/embed/'+$slides.eq(activeSlidePos).attr('data-yt-video-id'));
  		  }
  		  else{
  		    $slideActive.find('figure').before('<iframe width="589" height="331" src="//www.youtube.com/embed/'+$slides.eq(activeSlidePos).attr('data-yt-video-id')+'" frameborder="0" allowfullscreen></iframe>');
  		  }
  		  $slideActive.find('.img-crop').css('display','none');
  		  $slideActive.find('iframe').css('display','block');
  		}
  		else{
  		  $slideActive.find('.img-crop').css('display','block');
		    $slideActive.find('iframe').remove();
    		$slideActive.find('img').attr('src', $slides.eq(activeSlidePos).find('img').attr('src'));
  		}
	  	$slideActive.find('h2').html($slides.eq(activeSlidePos).find('h3 a').html());
	  	$slideActive.find('p').html($slides.eq(activeSlidePos).find('p.visuallyhidden a').html());
      $slideActive.find('h2').wrapInner( "<a href='#'></a>" ).find('a').attr('href', $slides.eq(activeSlidePos).find('p.visuallyhidden a').attr('href'));
	  	$slides.removeClass('active');
	  	$slides.eq(activeSlidePos).addClass('active');
	  	mediaAddView($slides.eq(activeSlidePos));
	  	changeShareBoxesUrlMedia($slides.eq(activeSlidePos).attr('data-media-meta-id'), $slides.eq(activeSlidePos).attr('data-media-blog-id'));
  	}
  	if(activeSlidePos == 0){
  		$buttons.eq(0).hide();
  	}
  	if(activeSlidePos < nbSlides - 1){
  		$buttons.eq(1).show();
  	}
  	return false;
  });

  $buttons.eq(1).on('click', function(){
  	if(activeSlidePos == nbSlides - 1){
  		$buttons.eq(1).hide();
  	} else {
  		$buttons.eq(1).show();
  		activeSlidePos++;

  		if($slides.eq(activeSlidePos).hasClass('video')){
  		  if($slideActive.find('iframe').length > 0){
  		    $slideActive.find('iframe').attr('src','//www.youtube.com/embed/'+$slides.eq(activeSlidePos).attr('data-yt-video-id'));
  		  }
  		  else{
  		    $slideActive.find('figure').before('<iframe width="589" height="331" src="//www.youtube.com/embed/'+$slides.eq(activeSlidePos).attr('data-yt-video-id')+'" frameborder="0" allowfullscreen></iframe>');
  		  }
  		  $slideActive.find('.img-crop').css('display','none');
  		  $slideActive.find('iframe').css('display','block');
  		}
  		else{
  		  $slideActive.find('.img-crop').css('display','block');
  		  $slideActive.find('iframe').remove();
  	  	$slideActive.find('img').attr('src', $slides.eq(activeSlidePos).find('img').attr('src'));
  	  }

	  	$slideActive.find('h2').html($slides.eq(activeSlidePos).find('h3 a').html());
	  	$slideActive.find('p').html($slides.eq(activeSlidePos).find('p.visuallyhidden a').html());
      $slideActive.find('h2').wrapInner( "<a href='#'></a>" ).find('a').attr('href', $slides.eq(activeSlidePos).find('p.visuallyhidden a').attr('href'));
	  	$slides.removeClass('active');
	  	$slides.eq(activeSlidePos).addClass('active');
	  	mediaAddView($slides.eq(activeSlidePos));
	  	changeShareBoxesUrlMedia($slides.eq(activeSlidePos).attr('data-media-meta-id'), $slides.eq(activeSlidePos).attr('data-media-blog-id'));
  	}
  	if(activeSlidePos == nbSlides - 1){
  		$buttons.eq(1).hide();
  	}
  	if(activeSlidePos > 0){
  		$buttons.eq(0).show();
  	}
  	return false;
  });

  $slides.on('click', function(){
  	$slides.removeClass('active');
  	jQuery(this).addClass('active');

  	mediaAddView(jQuery(this));
  	changeShareBoxesUrlMedia(jQuery(this).attr('data-media-meta-id'), jQuery(this).attr('data-media-blog-id'));

  	activeSlidePos = jQuery(this).index();

    if($slides.eq(activeSlidePos).hasClass('video')){
      if($slideActive.find('iframe').length > 0){
        $slideActive.find('iframe').attr('src','//www.youtube.com/embed/'+$slides.eq(activeSlidePos).attr('data-yt-video-id'));
      }
      else{
        $slideActive.find('figure').before('<iframe width="589" height="331" src="//www.youtube.com/embed/'+$slides.eq(activeSlidePos).attr('data-yt-video-id')+'" frameborder="0" allowfullscreen></iframe>');
      }
      $slideActive.find('.img-crop').css('display','none');
      $slideActive.find('iframe').css('display','block');
    }
    else{
      $slideActive.find('.img-crop').css('display','block');
      $slideActive.find('iframe').remove();
    	$slideActive.find('img').attr('src', $slides.eq(activeSlidePos).find('img').attr('src'));
  	}

  	$slideActive.find('h2').html($slides.eq(activeSlidePos).find('h3 a').html());
  	$slideActive.find('p').html($slides.eq(activeSlidePos).find('p.visuallyhidden a').html());
    $slideActive.find('h2').wrapInner( "<a href='#'></a>" ).find('a').attr('href', $slides.eq(activeSlidePos).find('p.visuallyhidden a').attr('href'));
  	if(activeSlidePos == 0){
  		$buttons.eq(0).hide();
  	} else {
  		$buttons.eq(0).show();
  	}

  	if(activeSlidePos == nbSlides - 1){
  		$buttons.eq(1).hide();
  	} else {
  		$buttons.eq(1).show();
  	}

  	return false;
  });

}//sliderPartners();

function sliderInline(){
  var $wrapper = jQuery('.inline-slideshow');
  var $container = $wrapper.find('.slider-container');
  var $slides = $wrapper.find('.slide');
  var $buttons = $wrapper.find('.buttons button');
  var slideWidth = $slides.eq(1).outerWidth(true);
  var nbSlides = $slides.length;
  var activeSlidePos = 0;
  var anim = true;
  var prev = false;

  if( nbSlides < 7 ){
	  $buttons.hide();
	  $wrapper.find('.slider').addClass('nonav');
  } else {
  	$buttons.eq(0).hide();
  }

  $container.width(slideWidth * nbSlides);

  $buttons.on('click', function(){
    prev = (jQuery(this).hasClass('prev') ? true : false);
    $buttons.show();
    if(anim){
      activeSlidePos = (prev ? activeSlidePos - 1 : activeSlidePos + 1);
	    if(prev && activeSlidePos == 0){
	    	$buttons.eq(0).hide();
	    };
	    if(!prev && activeSlidePos == nbSlides - 7){
	    	$buttons.eq(1).hide();
	    };
      anim = false;
      $slides.animate({
        left: '+=' + slideWidth * (prev ? 1 : -1)
      }, 500).promise().done(function(){
        anim = true;
      });
    }
  });

  $slides.on('click', function(){
    //return false;
  });
}//sliderPartners();

function sliderMicrosite(){
  var $wrapper = jQuery('.microsite-slideshow');
  var $container = $wrapper.find('.slider-container');
  var $slides = $wrapper.find('.slide');
  var $buttons = $wrapper.find('.buttons button');
  var slideWidth = $slides.eq(0).outerWidth(true);
  var nbSlides = $slides.length;
  var activeSlidePos = 0;
  var anim = true;
  var prev = false;

  $container.width(slideWidth * nbSlides);

  $buttons.on('click', function(){
    prev = (jQuery(this).hasClass('prev') ? true : false);
    if(prev && activeSlidePos == 0){ return false };
    if(!prev && activeSlidePos == nbSlides - 1){ return false };
    if(anim){
      anim = false;
      $slides.animate({
        left: '+=' + slideWidth * (prev ? 1 : -1)
      }, 500).promise().done(function(){
        anim = true;
        activeSlidePos = (prev ? activeSlidePos - 1 : activeSlidePos + 1);
      });
    }
  });
}//sliderMicrosite();

function mediaAddView(media){
  if(!jQuery(media).hasClass('viewed')){
    jQuery(media).addClass('viewed');
    $.ajax({
      type: 'POST',
      dataType: 'html',
      url: ajaxurl,
      data: {
        'action': 'newgalleryview', //calls wp_ajax_nopriv_ajaxlogin
        'media_meta_id' : jQuery(media).attr('data-media-meta-id'),
        'media_blog_id' : jQuery(media).attr('data-media-blog-id')
      },
      success: function(data){
        console.log(data);
      }
    });
  }
}

function changeShareBoxesUrlMedia(mediaId, blogId){
  var shareUrl = "";
  if(currentPageUrl.indexOf('?') != -1){
    shareUrl = currentPageUrl + '&media_id=' + mediaId + encodeURIComponent( '&blog_id=' + blogId );
  }
  else{
    shareUrl = currentPageUrl + '?media_id=' + mediaId + encodeURIComponent( '&blog_id=' + blogId );
  }
  changeShareBoxesUrl(shareUrl);
}

function changeShareBoxesUrl(url){
  jQuery('.social.condensed').css('display','');
  jQuery('.mailto').attr('href',"mailto:?subject=&body="+url);
  //jQuery('.g-plus').attr('data-href',url);
  jQuery(".g-plus-wrap").html('<div id="gplus"></div>');
  gapi.plus.render("gplus", { "href": url, action: "share", annotation:"bubble" });
  //jQuery('.fb-like').attr('data-href',url);
  jQuery('.fb-like').parent().remove();
  jQuery('.social.condensed').append('<div class="social-wrap"><div class="fb-like" data-href="'+url+'" data-width="150" data-layout="button_count" data-show-faces="false" data-send="false" data-action="recommend"></div></div>');
  FB.XFBML.parse();
  jQuery('.twitter-share-button').parent().remove();
  jQuery('.social.condensed').append('<div class="social-wrap"><a href="https://twitter.com/share" class="twitter-share-button" data-url="'+url+'" data-lang="fr" data-count="none">Tweeter</a></div>');
  twttr.widgets.load();
}

function toggleFilterBox(){
  var $filterbox = jQuery('.filterbox');
  var $button = $filterbox.find('.togglefilterbox');
  var $filterboxContent = $filterbox.find('.filterbox-content');
  var $title = $filterbox.find('h2');

  $title.css('cursor', 'pointer');

  $button.add($title).on('click', function(){
    $filterboxContent.toggle();
    $filterbox.toggleClass('active');
    if($filterbox.hasClass('active')){
      $button.text('Fermer les filtres');
    } else {
      $button.text('Ouvrir les filtres');
    }
  });
}//toggleFilterBox();

function tabs(){
  var $wrapper = jQuery('.tabs');
  var $target = jQuery('.tabs > ul');
  var $tabsTitle = $target.find('a');
  var $tabs = $wrapper.find('.tab');

  $wrapper.hide();
  $tabs.hide();

  $target.replaceWith('<div class="tabs-buttons"></div>');
  $newTarget = jQuery('.tabs .tabs-buttons');

  $tabsTitle.each(function(){
    $newTarget.append('<button>' + jQuery(this).get(0).innerHTML + '</button>');
  });

  var $buttons = $newTarget.find('button');

  $buttons.eq(0).addClass('active');
  $tabs.eq(0).show();

  $wrapper.show();

  $buttons.on('mousedown', function(){
    jQuery('.click').removeClass('click');
    jQuery(this).addClass('click');
  });

  $buttons.on('click', function(){
    index = jQuery(this).index();
    $tabs.hide();
    $tabs.eq(index).show();
    $buttons.removeClass('active');
    $buttons.eq(index).addClass('active');
  });
}//.tabs();

function regionsDropdown(){
  var html = '<select name="region_dropdown" id="region_dropdown"></select>';
  var $element = jQuery('.accessibledropdown');
  var $elements = $element.find('li a');

  $element.replaceWith(html);
  jQuery('#region_dropdown').append($elements);

  jQuery('#region_dropdown a').each(function(){
    var extras = "";
    if(jQuery(this).hasClass('selected')){
      extras += ' selected="selected"'
    }
    jQuery(this).replaceWith('<option value="' + jQuery(this).attr('href') + '" '+extras+'>' + jQuery(this).html() + '</option>');
  });
}//regionsDropdown();

function eventsTabs(){
  jQuery('.events-tabs .tabs-buttons button').removeClass('active');
  if( getURLParameter('type') == 'membres' ){
    jQuery('.events-tabs .tabs-buttons button:eq(1)').addClass('active');
    jQuery('.events-sections .events-section:eq(1)').css('display','block');
    jQuery('.events-sections .events-section:not(:eq(1))').css('display','none');
  }
  else if( getURLParameter('type') == 'centre' ){
    jQuery('.events-tabs .tabs-buttons button:eq(2)').addClass('active');
    jQuery('.events-sections .events-section:eq(2)').css('display','block');
    jQuery('.events-sections .events-section:not(:eq(2))').css('display','none');
  }
  else if( getURLParameter('type') == 'internationaux' ){
    jQuery('.events-tabs .tabs-buttons button:eq(3)').addClass('active');
    jQuery('.events-sections .events-section:eq(3)').css('display','block');
    jQuery('.events-sections .events-section:not(:eq(3))').css('display','none');
  }
  else {
    jQuery('.events-tabs .tabs-buttons button:eq(0)').addClass('active');
    jQuery('.events-sections .events-section:eq(0)').css('display','block');
    jQuery('.events-sections .events-section:not(:eq(0))').css('display','none');
  }

  jQuery(document).on('click', '.tabs-buttons button:eq(0)', function() {
    jQuery('.events-sections .events-section').css('display','none');
    jQuery('.events-sections .events-section:eq(0)').css('display','block');
  })
  jQuery(document).on('click', '.tabs-buttons button:eq(1)', function() {
    jQuery('.events-sections .events-section').css('display','none');
    jQuery('.events-sections .events-section:eq(1)').css('display','block');
  });
  jQuery(document).on('click', '.tabs-buttons button:eq(2)', function() {
    jQuery('.events-sections .events-section').css('display','none');
    jQuery('.events-sections .events-section:eq(2)').css('display','block');
  })
}//eventsTabs();


function eventsRegionsDropdown(){
  jQuery('.calendar #region_dropdown').on('change', function(){
    window.location.href = jQuery(this).val();
  });
}//eventsRegionsDropdown();

function multisiteDropdown(){
  jQuery('.microsite #editions').on('change', function(){
    window.location.href = jQuery(this).val();
  });
}//multisiteDropdown();

function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}

function equalHeights($obj) {
  var tempHeight = 0;

  for (var i = 0; i < $obj.length; i++) {
    var $thisHeight = jQuery($obj[ i ]).height();

    if ($thisHeight > tempHeight) tempHeight = $thisHeight;
  }
  $obj.css('height', tempHeight);
}
