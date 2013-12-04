<?php 
/*
Plugin Name: WAQ - Thèmes pour conférences
Plugin URI: http://webaquebec.org/
Version: 0.1.0
Description: Créé le custom taxonomy theme pour les conférences
Author: Paul Côté, Libéo
Author URI: http://www.libeo.com
*/

add_action( 'init', 'register_theme_post_type' );
add_action('theme_add_form_fields', 'theme_metabox_add', 10, 1);
add_action('theme_edit_form_fields', 'theme_metabox_edit', 10, 1);
add_action('created_theme', 'save_theme_meta_data', 10, 1);	
add_action('edited_theme', 'save_theme_meta_data', 10, 1);

function register_theme_post_type() {

  register_taxonomy(
  		'theme',
  		'session',
  		array(
  			'label' => 'Thème',
  			'labels' => array(
  			  'name' => 'Thèmes',
  			  'singular_name' => 'Thèmes',
  			  'all_items' => 'Tous les thèmes',
  			  'edit_item' => 'Modifier le thème',
  			  'view_item' => 'Voir le thème',
  			  'update_item' => 'Mettre à jour le thème',
  			  'add_new_item' => 'Ajouter un nouveau thème',
  			  'new_item_name' => 'Nouveau thème - Nom',
  			),
  			'public' => true,
  			'hierarchical' => true
  		)
  	);
  
}

function theme_metabox_add($tag) 
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
 
function theme_metabox_edit($tag) 
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

function save_theme_meta_data($term_id)
{
    if (isset($_POST['tax-order'])) 
		update_term_meta( $term_id, 'tax-order', $_POST['tax-order']);       
}