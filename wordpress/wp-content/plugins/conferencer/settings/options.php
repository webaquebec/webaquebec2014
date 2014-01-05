<?php

new Conferencer_Settings_Options();
class Conferencer_Settings_Options {
	var $defaults = array(
		'add_to_page' => true,
	);	
	
	function __construct() {
		register_activation_hook(CONFERENCER_REGISTER_FILE, array(&$this, 'activate'));
		register_deactivation_hook(CONFERENCER_REGISTER_FILE, array(&$this, 'deactivate'));
		add_action('admin_init', array(&$this, 'admin_init'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
		$this->options = array_merge($this->defaults, get_option('conferencer_options', array()));
	}
	
	function activate() {
		add_option('conferencer_options', array('add_to_page' => true));
	}

	function deactivate() {
		delete_option('conferencer_options');
	}

	function admin_init() {
		register_setting('conferencer_options', 'conferencer_options', array(&$this, 'validate'));
		add_settings_section('conferencer_options', "Conferencer Options", array(&$this, 'settings_header'), 'conferencer_options');
		add_settings_field('conferencer_options_add_to_page', "Add Content to Pages", array(&$this, 'show_field_add_to_page'), 'conferencer_options', 'conferencer_options');
		add_settings_field('conferencer_options_details_toggle', "Show Session Detail Toggle", array(&$this, 'show_field_details_toggle'), 'conferencer_options', 'conferencer_options');
	}
	
	function validate($input) {
		$this->options['add_to_page'] = isset($input['add_to_page']);
		$this->options['details_toggle'] = isset($input['details_toggle']);
		return $this->options;
	}
	
	function settings_header() { ?>
		<p>Change how Conferencer display information within your pages.</p>
	<?php }
	
	function show_field_add_to_page() { ?>
		<input
			type="checkbox"
			name="conferencer_options[add_to_page]"
			id="conferencer_options_add_to_page"
			<?php if ($this->options['add_to_page']) echo 'checked'; ?>
		/>
		<label for="conferencer_options_add_to_page">
			Add Content to Page
		</label>
	<?php }
	
	function show_field_details_toggle() { ?>
		<input
			type="checkbox"
			name="conferencer_options[details_toggle]"
			id="conferencer_options_details_toggle"
			<?php if ($this->options['details_toggle']) echo 'checked'; ?>
		/>
		<label for="conferencer_options_details_toggle">
			Show Session Detail Toggle
		</label>
	<?php }
	
	function admin_menu() {
		add_submenu_page(
			'conferencer',
			"Options",
			"Options",
			'edit_posts',
			'conferencer_options',
			array(&$this, 'settings')
		);
	}
	
	function settings() { ?>
		<form action="options.php" method="post">
			<?php
				settings_fields('conferencer_options');
				do_settings_sections('conferencer_options');
			?>
			<input type="submit" value="Save Settings" />
		</form>
	<?php }
}