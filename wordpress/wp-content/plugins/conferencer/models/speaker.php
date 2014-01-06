<?php

if( !class_exists('Conferencer_Speaker') ):

class Conferencer_Speaker extends Conferencer_CustomPostType {
	var $slug = 'speaker';
	var $archive_slug = 'speakers';
	var $singular = "Speaker";
	var $plural = "Speakers";
	var $menu_icon = "dashicons-businessman";

	var $speaker_cache = false;

	function set_options() {
		parent::set_options();

		$company_query = new WP_Query(array(
			'post_type' => 'company',
			'posts_per_page' => -1, // show all
			'orderby' => 'title',
			'order' => 'ASC',
		));

		$company_options = array();
		foreach ($company_query->posts as $company) {
			$company_options[$company->ID] = get_the_title($company->ID);
		}

		$this->options = array_merge($this->options, array(
			'title' => array(
				'type' => 'text',
				'label' => "Title",
			),
			'company' => array(
				'type' => 'select',
				'label' => "Company",
				'options' => $company_options,
			),
		));
	}

	function columns($columns) {
		$columns = parent::columns($columns);
		$columns['title'] = "Name";
		$columns['conferencer_speaker_title'] = "Title";
		$columns['conferencer_speaker_company'] = "Company";
		$columns['conferencer_speaker_sessions'] = "Sessions";
		return $columns;
	}

	function column($column) {
		parent::column($column);

		global $post;

		switch (str_replace('conferencer_speaker_', '', $column)) {
			case 'title':
				echo $post->title;
				break;
			case 'company':
				if ($post->company) echo "<a href='post.php?action=edit&post=$post->company'>".get_the_title($post->company)."</a>";
				break;
			case 'sessions':
				$links = array();
				foreach (Conferencer::get_sessions($post->ID) as $session) {
					$links[] = "<a href='post.php?action=edit&post=$session->ID'>".get_the_title($session->ID)."</a>";
				}

				echo implode(', ', $links);
				break;
		}
	}
}

endif; // class_exists check

new Conferencer_Speaker();