<?php

// TODO: Maybe use get_transient for caching?

if( !class_exists('Conferencer_Shortcode') ):

abstract class Conferencer_Shortcode {
	var $shortcode = 'conferencer_shortcode';
	var $defaults = array();
	var $options = array();
	var $buttons = array();

	static $first_button = true;

	function __construct() {
		add_shortcode($this->shortcode, array(&$this, 'shortcode'));
		add_filter('the_content', array(&$this, 'pre_add_to_page'));

		add_action('init', array(&$this, 'add_buttons'));

		add_action('save_post', array(&$this, 'save_post'));
		add_action('trash_post', array(&$this, 'trash_post'));

		register_activation_hook(CONFERENCER_REGISTER_FILE, array(&$this, 'activate'));
		register_deactivation_hook(CONFERENCER_REGISTER_FILE, array(&$this, 'deactivate'));

		global $wpdb;
		$wpdb->conferencer_shortcode_cache = $wpdb->prefix.'conferencer_shortcode_cache';
	}

	function shortcode($options) {
		$this->options = shortcode_atts($this->defaults, $options);
		$this->prep_options();

		if (!$content = $this->get_cache()) {
			$content = $this->content();
			$this->cache($content);
		}

		return $content;
	}

	function pre_add_to_page($content) {
		$options = get_option('conferencer_options');
		return $options['add_to_page'] ? $this->add_to_page($content) : $content;
	}

	function add_to_page($content) {
		return $content;
	}

	function prep_options() {
		foreach ($this->options as $key => $value) {
			if (is_string($value)) {
				if ($value == 'true') $this->options[$key] = true;
				if ($value == 'false') $this->options[$key] = false;
			}
		}
	}

	abstract function content();

	function add_buttons() {
		if (current_user_can('edit_posts') && current_user_can('edit_pages') && get_user_option('rich_editing')) {
			add_filter('mce_external_plugins', array(&$this, 'mce_plugins'));
			add_filter('mce_buttons', array(&$this, 'mce_buttons'));
		}
	}

	function mce_plugins($plugin) {
		foreach ($this->buttons as $button) {
			$plugin['conferencer_'.$button] = CONFERENCER_URL.'js/buttons/'.$button.'.js';
		}

		return $plugin;
	}

	function mce_buttons($buttons) {
		if (count($this->buttons) && Conferencer_Shortcode::$first_button) {
			$buttons[] = "|";
			Conferencer_Shortcode::$first_button = false;
		}

		foreach ($this->buttons as $button) {
			$buttons[] = 'conferencer_'.$button;
		}

		return $buttons;
	}

	// Caching ----------------------------------------------------------------

	// TODO: doesn't $wpdb need to be globalized in this function?
	function activate() {
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta("CREATE TABLE $wpdb->conferencer_shortcode_cache (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			shortcode text NOT NULL,
			options text NOT NULL,
			content mediumtext NOT NULL,
			UNIQUE KEY id(id)
		);");
	}

	function deactivate() {
		global $wpdb;
		$wpdb->query("drop table $wpdb->conferencer_shortcode_cache");
	}

	function save_post($post_id) {
		if (!in_array(get_post_type($post_id), Conferencer::$post_types)) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		self::clear_cache();
	}

	function trash_post($post_id) {
		if (!in_array(get_post_type($post_id), Conferencer::$post_types)) return;
		self::clear_cache();
	}

	function get_cache() {
		if (!get_option('conferencer_caching')) return false;

		global $wpdb;
		return $wpdb->get_var($wpdb->prepare(
			"SELECT content
			from $wpdb->conferencer_shortcode_cache
			where shortcode = %s
			and options = %s",
			$this->shortcode,
			serialize($this->options)
		));
	}

	function cache($content) {
		global $wpdb;
		$wpdb->insert($wpdb->conferencer_shortcode_cache, array(
			'created' => current_time('mysql'),
			'shortcode' => $this->shortcode,
			'options' => serialize($this->options),
			'content' => $content,
		));
	}

	static function get_all_cache() {
		global $wpdb;
		return $wpdb->get_results("SELECT shortcode, count(id) AS count FROM $wpdb->conferencer_shortcode_cache GROUP BY shortcode", OBJECT);
	}

	static function clear_cache() {
		global $wpdb;
		$wpdb->query("TRUNCATE $wpdb->conferencer_shortcode_cache");
	}
}

endif; // class_exists check