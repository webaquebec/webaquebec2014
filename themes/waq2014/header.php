<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<title><?php wp_title( ' | ', true, 'right' ); ?></title>

	<?php wp_head(); ?>

	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript">
  	<?php
  		/**
  		 * Les Javascripts et les CSS ne doivent pas être inclus ici!
  		 * Ils doivent être ajouter dans la fonction add_my_scripts() et add_my_styles()
  		 * dans le fichier functions.php (hint, allez voir la fichier functions.php)		 *
  		 */
  		 $save_nonce = wp_create_nonce("save_user_sessions_nonce");
  		 $get_nonce = wp_create_nonce("get_user_sessions_nonce");
  		 $init_nonce = wp_create_nonce("fb_init_nonce");
  		 echo 'var save_user_sessions_ajax = "'.admin_url('admin-ajax.php?action=save_user_sessions&nonce='.$save_nonce).'"'."\n";
  		 echo 'var get_user_sessions_ajax = "'.admin_url('admin-ajax.php?action=get_user_sessions&nonce='.$get_nonce).'"'."\n";
  		 echo 'var fb_init_ajax = "'.admin_url('admin-ajax.php?action=fb_init&nonce='.$init_nonce).'"'."\n";
  		 echo 'var user_sessions = '.json_encode(get_user_sessions()).''."\n";
  		 
  	?>
	</script>

</head>
<body>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
      appId      : '1382147128676757',                   // App ID from the app dashboard
      status     : true,                                 // Check Facebook Login status
      xfbml      : true,                                  // Look for social plugins on the page
      cookie : true
    });
    
    FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        facebook_connected = true;
        fb_init_php(function(){get_user_sessions();});
      }
      else{
        get_user_sessions();
        load_sessions();
      }
    });

    // Additional initialization code such as adding Event Listeners goes here
  };

  // Load the SDK asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

<!-- Page wrapper -->
<div class="l-page-wrapper" itemscope itemtype="http://schema.org/Event">

  <?php 
    if(is_front_page()){
    }
    else{
      get_template_part('header','page');
    }
    
    