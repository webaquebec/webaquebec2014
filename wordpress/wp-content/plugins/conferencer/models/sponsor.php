<?php

if( !class_exists('Conferencer_Sponsor') ):

class Conferencer_Sponsor extends Conferencer_CustomPostType {
	var $slug = 'sponsor';
	var $archive_slug = 'sponsors';
	var $singular = "Sponsor";
	var $plural = "Sponsors";
	var $menu_icon = "dashicons-awards";

	function set_options() {
		parent::set_options();

		$sponsor_level_query = new WP_Query(array(
			'post_type' => 'sponsor_level',
			'posts_per_page' => -1, // get all
		));

		$level_options = array();
		foreach ($sponsor_level_query->posts as $sponsor_level) {
			$level_options[$sponsor_level->ID] = $sponsor_level->post_title;
		}

		$this->options = array_merge($this->options, array(
			'url' => array(
				'type' => 'text',
				'label' => "URL",
			),
			'level' => array(
				'type' => 'select',
				'label' => "Level",
				'options' => $level_options,
			),
		));
	}

	function columns($columns) {
		$columns = parent::columns($columns);
		$columns['conferencer_sponsor_level'] = "Level";
		$columns['conferencer_sponsor_url'] = "URL";
		return $columns;
	}

	function column($column) {
		parent::column($column);

		global $post;

		switch (str_replace('conferencer_sponsor_', '', $column)) {
			case 'level':
				if ($post->level) echo "<a href='post.php?action=edit&post=$post->level'>".get_the_title($post->level)."</a>";
				break;
			case 'url':
				if ($post->url) echo "<a href='$post->url' target='_blank'>$post->url</a>";
				break;
		}
	}
}

endif; // class_exists check

new Conferencer_Sponsor();