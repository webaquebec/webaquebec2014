<?php
/*
Theme Name: WP Simple
Theme URI: http://libeo.com/
Description: Un thème Wordpress de base, sans style, mais avec tous les templates prêts pour l'intégration
Author: Danny Turcotte <danny.turcotte@libeo.com>
Author URI: http://libeo.com/
Version: 1.0
Tags: minimal, no-ui

Veuillez utiliser les standards de code de Wordpress
https://codex.wordpress.org/WordPress_Coding_Standards

Pour valider la conformité du code, veuillez utiliser PHPCodeSniffer et le standard Wordpress
*/

require_once('lib/aqua-resizer/aq_resizer.php');
require('lib/facebook/src/facebook.php');

// Bonne pratique de mettre tous les add_action et add_filter dans le haut!
add_action( 'wp_enqueue_scripts', 'add_my_scripts' );
add_action( 'wp_enqueue_scripts', 'add_my_styles' );
add_action( 'init', 'add_my_menus' );
add_action( 'init', 'facebook_init' );

add_filter( 'wp_title', 'my_title', 10, 2 );
add_filter( 'the_generator', 'remove_generator' );
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

add_filter('get_terms', 'get_terms_ordered', 10, 3);

add_action("wp_ajax_save_user_sessions", "save_user_sessions");
add_action("wp_ajax_nopriv_save_user_sessions", "save_user_sessions");
add_action("wp_ajax_fb_init", "facebook_init");
add_action("wp_ajax_nopriv_fb_init", "facebook_init");
add_action("wp_ajax_get_user_sessions", "user_sessions");
add_action("wp_ajax_nopriv_get_user_sessions", "user_sessions");


function add_my_scripts() {
    // WordPress possède un version de jQuery. Ce n'est pas toujours la plus récente
    // Ajouter la version (qui sera chargé en No Conflict) WordPress de jQuery
    //wp_enqueue_script('jquery');
    
  setlocale(LC_ALL, 'fr_CA.utf-8');

	//Mettre la version la plus à jour de jQuery
	wp_deregister_script( 'jquery' );
	
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/vendor/modernizr-2.6.2.min.js', array(), '2.6.2', false );
	
	wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', array(), '1.10.2', true );
	wp_enqueue_script( 'googlemaps', 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing&language=fr', array(), null, true );
	wp_enqueue_script( 'waq-moment', get_template_directory_uri() . '/js/vendor/moment.min.js', array(), null, true );
	wp_enqueue_script( 'waq-underscore', get_template_directory_uri() . '/js/vendor/underscore-min.js', array(), null, true );
	
	wp_enqueue_script( 'waq-buttonize', get_template_directory_uri() . '/js/plugins/buttonize.jquery.js', array(), null, true );
	wp_enqueue_script( 'waq-eminize', get_template_directory_uri() . '/js/plugins/eminize.jquery.js', array(), null, true );
	wp_enqueue_script( 'waq-resize', get_template_directory_uri() . '/js/plugins/onfontresize.jquery.js', array(), null, true );
	
	wp_enqueue_script( 'waq-schedules', get_template_directory_uri() . '/js/app/objects/Schedule.js', array(), null, true );
	wp_enqueue_script( 'waq-usersessions', get_template_directory_uri() . '/js/app/objects/CustomSchedule.js', array(), null, true );
	wp_enqueue_script( 'waq-countdown', get_template_directory_uri() . '/js/app/objects/Countdown.js', array(), null, true );
	wp_enqueue_script( 'waq-snapmenu', get_template_directory_uri() . '/js/app/objects/SnapMenu.js', array(), null, true );
	wp_enqueue_script( 'waq-mobilemenu', get_template_directory_uri() . '/js/app/objects/MobileMenu.js', array(), null, true );
	wp_enqueue_script( 'waq-slideshow', get_template_directory_uri() . '/js/app/objects/Slideshow.js', array(), null, true );
	wp_enqueue_script( 'waq-googlemaps', get_template_directory_uri() . '/js/app/objects/GoogleMaps.js', array(), null, true );
	
	wp_enqueue_script( 'waq-common', get_template_directory_uri() . '/js/app/WAQ.Common.js', array(), null, true );
	wp_enqueue_script( 'waq-main', get_template_directory_uri() . '/js/main.js', array(), null, true );
	//wp_enqueue_script( 'facebookbkp', get_template_directory_uri() . '/js/app/objects/facebookbkp.js', array(), null, true );
}

function add_my_styles() {
	// *Le fichier style.css est inclus automatiquement par Wordpress pas besoin de le réinclure
	// http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	wp_enqueue_style( 'fonts', 'http://fonts.googleapis.com/css?family=Lusitana:400,700|Roboto:400,300,500,700,900|Roboto+Condensed:400,700,300' );
	wp_enqueue_style( 'styles', get_template_directory_uri() . '/css/styles.css' );
}


function add_my_menus() {
	register_nav_menus(
	    array( 'menu-principal' => 'Menu principal du site web', )
	);
}

$facebook = null;
function facebook_init() {

  global $facebook;
  
  $facebook_conf = array(
    'appId'  => 'XXXXXXXXXXXX',
    'secret' => 'XXXXXXXXXXXXXXXXXXXXXXXX',
    'cookie' => true
  );
  
  if($_SERVER['SERVER_NAME'] == 'waq2014.job.paulcote.net'){
    $facebook_conf = array(
      'appId'  => '1382147128676757',
      'secret' => 'ed369652e881a20feae7beb8961f3f5f',
      'cookie' => true
    );
  }
  else if($_SERVER['SERVER_NAME'] == 'waq2014.dev.libeo.com'){
    $facebook_conf = array(
      'appId'  => '1421838541381572',
      'secret' => '30e1d430b36dafff1be00f753a979b27',
      'cookie' => true
    );
  }
  
	
	$facebook = new Facebook($facebook_conf);
}


// Modifier le titre pour afficher plus d'informations pertinentes
function my_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name.
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );

	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= $sep . $site_description;
	}

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) {
		$title .= $sep . sprintf( __( 'Page %s', 'wp-simple' ), max( $paged, $page ) );
	}

	return $title;
}


// Enlever la version de Wordpress dans le head, question de sécurité
function remove_generator() {
	return '';
}

// Ajouter le Walker Accessible s'il est disponible
function my_wp_nav_menu_args( $args = '' ) {

	if ( class_exists( 'Accessible_Walker' ) ) {
		$args['walker'] = new Accessible_Walker();
	}

	return $args;
}

function get_excerpt_by_id($post_id){
  $the_post = get_post($post_id); //Gets post ID
  $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
  $excerpt_length = 35; //Sets excerpt length by word count
  $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
  $words = explode(' ', $the_excerpt, $excerpt_length + 1);
  if(count($words) > $excerpt_length) {
    array_pop($words);
    array_push($words, '…');
    $the_excerpt = implode(' ', $words);
  }
  $the_excerpt = '<p>' . $the_excerpt . '</p>';
  return $the_excerpt;
}


/* From custom taxonomy sort plugin, get_terms function */
function get_terms_ordered($terms, $taxonomies, $args)
{

	// Controls behavior when get_terms is called at unusual times resulting in a terms array without objects
	$empty = false;

	// Create collector arrays
	$ordered_terms = array();
	$unordered_terms = array();

	// Add taxonomy order to terms
	foreach($terms as $term)
	{
		// Only set tax_order if value is an object
		if(is_object($term))
		{
			if($taxonomy_sort = get_term_meta($term->term_id, 'tax-order', true))
			{
				$term->tax_order = (int) $taxonomy_sort;
				$ordered_terms[] = $term;
			}
			else
			{
				// This catches any terms that don't have tax-order set
				$term->tax_order = (int) 0;
				$unordered_terms[] = $term;
			}
		}
		else
			$empty = true;
	}

	// Only sort by tax_order if there are items to sort, otherwise return the original array
	if(!$empty && count($ordered_terms) > 0)
		terms_quickSort($ordered_terms);
	else
		return $terms;

	// Combine the newly ordered items with the unordered items and return
	return array_merge($ordered_terms, $unordered_terms);
}

/**
 * Thanks to Paul Dixon (http://stackoverflow.com/questions/1462503/sort-array-by-object-property-in-php).
 * From custom taxonomy sort plugin, quickSort function
 */
function terms_quickSort(&$array)
{
	$cur = 1;
	$stack[1]['l'] = 0;
	$stack[1]['r'] = count($array)-1;

	do
	{
		$l = $stack[$cur]['l'];
		$r = $stack[$cur]['r'];
		$cur--;

		do
		{
			$i = $l;
			$j = $r;
			$tmp = $array[(int)( ($l+$r)/2 )];

			// partion the array in two parts.
			// left from $tmp are with smaller values,
			// right from $tmp are with bigger ones
			do
			{
				while( $array[$i]->tax_order < $tmp->tax_order )
				$i++;

				while( $tmp->tax_order < $array[$j]->tax_order )
			 	$j--;

				// swap elements from the two sides
				if( $i <= $j)
				{
					 $w = $array[$i];
					 $array[$i] = $array[$j];
					 $array[$j] = $w;

			 		$i++;
			 		$j--;
				}

			}while( $i <= $j );

			if( $i < $r )
			{
				$cur++;
				$stack[$cur]['l'] = $i;
				$stack[$cur]['r'] = $r;
			}
			$r = $j;

		}while( $l < $r );

	}while( $cur != 0 );
}

function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function get_user_sessions() {

    global $facebook;
    
    $uid = $facebook->getUser();
    
    if(!empty($uid)){
    
      $user_sesssions = get_transient( 'fb_'.$uid.'_sessions');
      
      if(!empty($user_sesssions)){
        return $user_sesssions;
      }
      else {
        return array();
      }
    }
    else {
      return array();
    }
}

function user_sessions() {
  echo json_encode(get_user_sessions());die;
}

function save_user_sessions() {

    global $facebook;
    
    /*if ( !wp_verify_nonce( $_REQUEST['nonce'], "save_user_sessions_nonce")) {
      exit("Not authorized");
    }*/
    
    $uid = $facebook->getUser();
    
    if(!empty($uid)){
    
      $user_sessions = json_decode($_REQUEST['user_sessions']);
    
      set_transient( 'fb_'.$uid.'_sessions', $user_sessions );
      
      echo json_encode($user_sessions); die;
    }
}

function parse_signed_request($signed_request) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  // confirm the signature
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}

function metas_facebook_og(){

  global $post;
    
  setlocale(LC_ALL, 'fr_CA.utf-8');

  $names = array(
    'dcterms.title' => get_bloginfo('name'),
    'description' => "Le Web à Québec c'est trois jours de rencontres par et pour les gens qui imaginent le web.",
    'dcterms.description' => "Le Web à Québec c'est trois jours de rencontres par et pour les gens qui imaginent le web."
  );
  
  $properties = array(
    'og:title' => get_bloginfo('name'),
    'og:description' => "Le Web à Québec c'est trois jours de rencontres par et pour les gens qui imaginent le web.",
    'og:image'=>get_bloginfo('template_directory')."/img/fb-image.png",
    'og:type' => 'website',
    'fb:app_id' => ''
  );
  
  if($_SERVER['SERVER_NAME'] == 'waq2014.job.paulcote.net'){
    $properties['fb:app_id'] = '1382147128676757';
  }
  else if($_SERVER['SERVER_NAME'] == 'waq2014.dev.libeo.com'){
    $properties['fb:app_id'] = '1421838541381572';
  }
                  
  if(is_singular('session')){
  
  
  	$time_slot_id = get_post_meta($post->ID, '_conferencer_time_slot', true);
    $session_start_unix = get_post_meta($time_slot_id, '_conferencer_starts', true);
    $session_ends_unix = get_post_meta($time_slot_id, '_conferencer_ends', true);
    $speakers_ids = get_post_meta($post->ID, '_conferencer_speakers', true);
    $room_id = get_post_meta($post->ID, '_conferencer_room', true);
    $session_room = get_the_title($room_id);
    
    $speaker_name = "";
    if(count($speakers_ids) > 1){
    	$speaker_name = "Panel";
    }
    else if(count($speakers_ids) == 1){
      $speaker_name = get_the_title(array_shift($speakers_ids));
    }
  
    $session_desc = "";
    if($speaker_name == 'Panel'){
      $session_desc .= "Un panel présenté";
    }
    else if(empty($speaker_name)){
      $session_desc .= "Une conférence présentée";
    }
    else{
      $session_desc .= "Une conférence de ". $speaker_name ." présentée";
    }
    $session_desc .= " le ".strtolower(strftime("%A %e %B",$session_start_unix));
    $session_desc .= " de ".trim(strftime("%kh%M",$session_start_unix))." à ".trim(strftime("%kh%M",$session_ends_unix));
    
    if(strpos(strtolower($session_room), "salle") === false){
      $session_desc .= " dans le ";
    }
    else{
      $session_desc .= " dans la ";
    }
    $session_desc .= str_replace('Salle', 'salle', $session_room);
    $session_desc .= ".";
    
    
  
    $names['dcterms.title'] = get_the_title( $post->ID );
    //$names['description'] = strip_tags(get_excerpt_by_id( $post->ID ));
    //$names['DC.description'] = strip_tags(get_excerpt_by_id( $post->ID ));
    $names['description'] = $session_desc;
    $names['dcterms.description'] = $session_desc;
    
    
    $properties['og:url'] = get_permalink( $post->ID );
    $properties['og:site_name'] = get_bloginfo('name');
    $properties['og:title'] = get_the_title( $post->ID );
    //$ogs['description'] = strip_tags(get_excerpt_by_id( $post->ID ));
    $properties['og:description'] = $session_desc;
    $properties['og:type'] = 'event';
    $properties['event:start_time'] = strftime("%Y-%m-%dT%H:%M",$session_start_unix);
    $properties['event:end_time'] = strftime("%Y-%m-%dT%H:%M",$session_ends_unix);
    $properties['event:location'] = $session_room;
  }
  else if(is_home()){
    $properties['og:url'] = get_bloginfo('url');
  }
  
  displayMetas($names,$properties);
}


/**
 * This function takes an associative array in parameter and displays a meta and opengraph meta 
 * with the name and content of each array element.
 * @param array $metas formated like "metaname" => "metavalue"
 */
function displayMetas( $names = array(), $properties = array() )
{
    foreach( $names as $k => $v )
    {
        echo "<meta name=\"{$k}\" content=\"{$v}\" />\n\t";
    }

    foreach( $properties as $k => $v )
    {
        echo "<meta property=\"{$k}\" content=\"{$v}\" />\n\t";
    }
}

function custom_wrapper_classes(){
    if(is_front_page()){
      echo ' homepage';
    }
    else if(is_singular('session')){
      echo ' single-page';
    }
    else{
      echo ' single-page';
    }
}


function fb_move_admin_bar() {
    echo '
    <style type="text/css">
    body { 
        margin-top: -28px;
        padding-bottom: 28px;
    }
    </style>';
}
// on backend area
add_action( 'admin_head', 'fb_move_admin_bar' );
// on frontend area
add_action( 'wp_head', 'fb_move_admin_bar' );