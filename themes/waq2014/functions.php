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

// Bonne pratique de mettre tous les add_action et add_filter dans le haut!
add_action( 'wp_enqueue_scripts', 'add_my_scripts' );
add_action( 'wp_enqueue_scripts', 'add_my_styles' );
add_action( 'init', 'add_my_menus' );

add_filter( 'wp_title', 'my_title', 10, 2 );
add_filter( 'the_generator', 'remove_generator' );
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

add_filter('get_terms', 'get_terms_ordered', 10, 3);


function add_my_scripts() {
    // WordPress possède un version de jQuery. Ce n'est pas toujours la plus récente
    // Ajouter la version (qui sera chargé en No Conflict) WordPress de jQuery
    //wp_enqueue_script('jquery');
    
  setlocale(LC_ALL, 'fr_CA.utf-8');

	//Mettre la version la plus à jour de jQuery
	wp_deregister_script( 'jquery' );
	wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js', array(), '1.9.0', false );
	wp_enqueue_script( 'resize', get_template_directory_uri() . '/js/vendor/jquery.onfontresize.js', array(), '1.0.0', false );
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/vendor/modernizr-2.6.2.min.js', array(), '2.6.2', false );
	wp_enqueue_script( 'main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );
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
