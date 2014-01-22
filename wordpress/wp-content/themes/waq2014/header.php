<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7 no-js" lang="fr">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8 no-js" lang="fr">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html class="no-js" lang="fr">
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<title><?php wp_title( ' | ', true, 'right' ); ?></title>

	<?php metas_facebook_og(); ?>

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
  		 echo 'var base_url = "'.get_bloginfo('url').'"'."\n";
  		 echo 'var template_url = "'.get_bloginfo('template_directory').'"'."\n";

  	?>
	</script>

</head>
<body class="not-zoomed">

<?php $post = get_post_meta( get_queried_object_id() );
$room = $post['_conferencer_room'];
$room_id = $room[0];
$room_post = get_post( $room_id );
$room_name = ' ' . $room_post->post_name;?>

<!-- Page wrapper -->
<div class="l-page-wrapper<?php echo custom_wrapper_classes() . $room_name ?>" itemscope itemtype="http://schema.org/Event">

  <?php
    if(is_front_page()){
    }
    else{
      get_template_part('header','page');
    }

