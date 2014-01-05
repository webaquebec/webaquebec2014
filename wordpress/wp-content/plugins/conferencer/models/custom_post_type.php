<?php

if( !class_exists('Conferencer_CustomPostType') ):

class Conferencer_CustomPostType {
	var $slug = 'custom_post_type';
	var $archive_slug = false; // use pluralized string if you want an archive page
	var $singular = "Item";
	var $plural = "Items";

	var $options = array();

	function __construct() {
		add_action('init', array(&$this, 'register_post_type'));
		add_action('init', array(&$this, 'set_options'));
		add_action('admin_enqueue_scripts', array(&$this, 'includes'));
		add_action('trash_post', array(&$this, 'detail_trash'));
		add_action('admin_init', array(&$this, 'meta_boxes'));
		add_action('save_post', array(&$this, 'save_post'));
		add_action('manage_edit-'.$this->slug.'_columns', array(&$this, 'columns'));
		add_action('manage_posts_custom_column', array(&$this, 'column'));
		add_action('after_setup_theme', array(&$this, 'add_image_sizes'));

		Conferencer::$post_types[] = $this->slug;

		$this->options['order'] = array(
			'type' => 'int',
			'label' => "Order",
		);
	}

	function register_post_type() {
		register_post_type($this->slug, array(
			'labels' => array(
				'name' => $this->plural,
				'singular_name' => $this->singular,
				'add_new' => "Add New $this->singular",
				'add_new_item' => "Add New $this->singular",
				'edit_item' => "Edit $this->singular",
				'new_item' => "New $this->singular",
				'view_item' => "View $this->singular",
				'search_items' => "Search $this->plural",
				'not_found' => "No $this->plural found",
				'not_found_in_trash' => "No $this->plural found in Trash",
			),
			'public' => true,
			'menu_position' => 42,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'comments', 'revisions'),
			'has_archive' => $this->archive_slug,
			'menu_icon' => $this->menu_icon,
			'rewrite' => array(
				'slug' => $this->archive_slug,
				'with_front' => false,
			),
		));
	}

	function includes() {
		if (isset($GLOBALS['post_type']) && in_array($GLOBALS['post_type'], Conferencer::$post_types)) {
			wp_enqueue_script('conferencer-cpt');
		}
	}

	function set_options() {
		// no action
	}

	function detail_trash($post_id) {
		// no action
	}

	function meta_boxes() {
		if (count($this->options)) add_meta_box(
			$this->slug."-options",
			"Conferencer Details",
			array($this, 'options'),
			$this->slug,
			'side'
		);
	}

	function options($post) {
		wp_nonce_field(plugin_basename(__FILE__), 'conferencer_nonce');
		include CONFERENCER_PATH.'/markup/options.php';
	}

	function save_post($post_id) {
		if (get_post_type($post_id) != $this->slug) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (isset($_POST['conferencer_nonce']) && !wp_verify_nonce($_POST['conferencer_nonce'], plugin_basename(__FILE__))) return;
		if (!current_user_can('edit_post', $post_id)) return;

		foreach($this->options as $key => $option) {
			if ($option['type'] == 'internal') continue;

			if(isset($_POST['conferencer_'.$key])){
			  $value = deep_trim($_POST['conferencer_'.$key]);
			}
			else {
			  $value = "";
			}

			if ($option['type'] == 'int') $value = intval($value);
			if ($option['type'] == 'money') $value = floatVal($value);
			if ($option['type'] == 'multi-select') {
				$values = array();
				if(isset($_POST['conferencer_'.$key])){
  				foreach ($_POST['conferencer_'.$key] as $value) {
  					if (!empty($value)) $values[] = $value;
  				}
				}
				$value = $values;
			}
			if ($option['type'] == 'date-time') {
			  $value = 0;
			  if(isset($_POST['conferencer_'.$key])){
  				$date = getdate(strtotime($_POST['conferencer_'.$key]['date']));
  				$time = getdate(strtotime($_POST['conferencer_'.$key]['time']));
  				$value = mktime(
  					$time['hours'],
  					$time['minutes'],
  					$time['seconds'],
  					$date['mon'],
  					$date['mday'],
  					$date['year']
  				);
				}
			}

			update_post_meta($post_id, '_conferencer_'.$key, $value);
		}
	}

	function columns($columns) {
		unset($columns['date']);
		return $columns;
	}

	function column($column) {
		global $post;

		Conferencer::add_meta($post);

		switch (str_replace('conferencer_'.$this->slug.'_', '', $column)) {
			case 'session_count':
				echo $post->non_session	? "not allowed"	: count(Conferencer::get_sessions($post->ID));
				break;
		}
	}

	function add_image_sizes() {
		// do nothing
	}

}

endif; // class_exists check