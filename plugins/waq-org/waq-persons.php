<?php 
/*
Plugin Name: WAQ - Personnes
Plugin URI: http://webaquebec.org/
Version: 0.1.0
Description: Créé le custom post type personne pour les organisateurs et bénévoles
Author: Paul Côté, Libéo
Author URI: http://www.libeo.com
*/

add_action( 'init', 'register_person_post_type' );
add_action('categorypersons_add_form_fields', 'categorypersons_metabox_add', 10, 1);
add_action('categorypersons_edit_form_fields', 'categorypersons_metabox_edit', 10, 1);
add_action('created_categorypersons', 'save_class_meta_data', 10, 1);	
add_action('edited_categorypersons', 'save_class_meta_data', 10, 1);

function register_person_post_type() {

  $settings = get_option('settings');

  register_taxonomy(
  		'categorypersons',
  		'person',
  		array(
  			'label' => 'Catégorie',
  			'labels' => array(
  			  'name' => 'Catégories',
  			  'singular_name' => 'Catégorie',
  			  'all_items' => 'Toute les catégories',
  			  'edit_item' => 'Modifier la catégorie',
  			  'view_item' => 'Voir la catégorie',
  			  'update_item' => 'Mettre à jour la catégorie',
  			  'add_new_item' => 'Ajouter une nouvelle catégorie',
  			  'new_item_name' => 'Nouvelle catégorie - Nom',
  			),
  			'public' => true,
  			'hierarchical' => true
  		)
  	);

  register_post_type('person', array(
    'labels' => array(
      'name' => 'Personnes',
      'singular_name' => 'Personne',
      'add_new' => 'Ajouter une personne',
      'add_new_item' => 'Ajouter une personne',
      'edit_item' => 'Modifier cette personne',
      'new_item' => 'Nouvelle personne',
      'view_item' => 'Voir cette personne',
      'search_items' => 'Trouver des personnes',
      'not_found' => 'Aucune personne',
      'not_found_in_trash' => 'Aucune personne dans la corbeille'
    ),
    'public' => true,
    'has_archive' => true,
    'supports' => array(
      'title',
      'page-attributes',
      'thumbnail'
    )
  ));
  
  if(function_exists("register_field_group")){
    if(!class_exists('acf_repeater_plugin')){
      include_once('acf-add-ons/acf-repeater/acf-repeater.php');
    }
    
  }
  else{
    wp_die('Advanced Custom Fields is needed for this extension to function fully');
  }
  
}

function categorypersons_metabox_add($tag) 
{
?>
	<div class="form-field">
		<label for="tax-order">Order</label>
 
		<p class="description"></p>
	</div>
 
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="tax-order"></label>
		</th>
		<td>
			<input name="tax-order" id="tax-order" type="text" value="" size="40" aria-required="true" />
			<p class="description"></p>
		</td>
	</tr>
<?php
}
 
function categorypersons_metabox_edit($tag) 
{
?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="tax-order">Order</label>
		</th>
		<td>
			<input name="tax-order" id="tax-order" type="text" value="<?php echo get_term_meta($tag->term_id, 'tax-order', true); ?>" size="40" aria-required="true" />
			<p class="description"></p>
		</td>
	</tr>
<?php
}

function save_class_meta_data($term_id)
{
    if (isset($_POST['tax-order'])) 
		update_term_meta( $term_id, 'tax-order', $_POST['tax-order']);       
}