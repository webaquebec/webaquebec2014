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
	<?php
		/**
		 * Les Javascripts et les CSS ne doivent pas être inclus ici!
		 * Ils doivent être ajouter dans la fonction add_my_scripts() et add_my_styles()
		 * dans le fichier functions.php (hint, allez voir la fichier functions.php)		 *
		 */
	?>

</head>
<body>

<!-- Page wrapper -->
<div class="l-page-wrapper" itemscope itemtype="http://schema.org/Event">

  <?php 
    if(is_front_page()){
    }
    else{
      get_template_part('header','page');
    }
    
    