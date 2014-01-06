<?php

if( !class_exists('Conferencer_Sponsor_Level') ):

class Conferencer_Sponsor_Level extends Conferencer_CustomPostType {
	var $slug = 'sponsor_level';
	var $archive_slug = 'sponsor-levels';
	var $singular = "Sponsor Level";
	var $plural = "Sponsor Levels";
	var $menu_icon = "dashicons-awards";

	function set_options() {
		parent::set_options();

		$this->options = array_merge($this->options, array(
			'logo_width' => array(
				'type' => 'int',
				'label' => "Logo Width",
			),
			'logo_height' => array(
				'type' => 'int',
				'label' => "Logo Height",
			),
		));
	}

	function add_image_sizes() {
	 	parent::add_image_sizes();

		foreach (get_posts(array(
			'post_type' => $this->slug,
			'numberposts' => -1, // get all
		)) as $level) {
			add_image_size(
				"sponsor_level_$level->ID",
				get_post_meta($level->ID, '_conferencer_logo_width', true),
				get_post_meta($level->ID, '_conferencer_logo_height', true)
			);
		}
	}
}

endif; // class_exists check

new Conferencer_Sponsor_Level();