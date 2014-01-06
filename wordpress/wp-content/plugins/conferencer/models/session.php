<?php

if( !class_exists('Conferencer_Session') ):

class Conferencer_Session extends Conferencer_CustomPostType {
	var $slug = 'session';
	var $archive_slug = 'sessions';
	var $singular = "Session";
	var $plural = "Sessions";
	var $menu_icon = "dashicons-calendar";

	function set_options() {
		parent::set_options();

		$this->options = array_merge($this->options, array(
			'keynote' => array(
				'type' => 'boolean',
				'label' => "Keynote",
			),
			'room' => array(
				'type' => 'select',
				'label' => "Room",
				'options' => array(), // set below
			),
			'time_slot' => array(
				'type' => 'select',
				'label' => "Time Slot",
				'options' => array(), // set below
			),
			'track' => array(
				'type' => 'select',
				'label' => "Track",
				'options' => array(), // set below
			),
			'speakers' => array(
				'type' => 'multi-select',
				'label' => "Speakers",
				'options' => array(), // set below
			),
			'sponsors' => array(
				'type' => 'multi-select',
				'label' => "Sponsors",
				'options' => array(), // set below
			),
		));

		foreach ($this->options as $key => $option) {
			foreach (Conferencer::get_posts($key, false, 'title_sort') as $post) {
				$text = $post->post_title;

				if ($key == 'time_slot') {
					Conferencer::add_meta($post);
					if ($post->non_session) continue;
					if ($post->starts) {
						$text = date('n/j/y, g:iA', $post->starts);
						if ($post->ends) $text .= ' &ndash; '.date('g:iA', $post->ends);
					} else $text = 'unscheduled';
				}

				$this->options[$key]['options'][$post->ID] = $text;
			}
		}
	}

	function detail_trash($post_id) {
		parent::detail_trash($post_id);

		$post_type = get_post_type($post_id);
		$detached = array();

		foreach (Conferencer::get_sessions($post_id) as $session) {
			Conferencer::add_meta($session);
			if (in_array($post_type, array('speaker', 'sponsor'))) {
				$type = $post_type.'s';
				if (in_array($post_id, $session->$type)) {
					update_post_meta($session->ID, "_conferencer_$type", array_diff($session->$type, array($post_id)));
					$detached[] = $session;
				}
			} else if ($session->$post_type == $post_id) {
				update_post_meta($session->ID, "_conferencer_$post_type", false);
				$detached[] = $session;
			}
		}

		foreach ($detached as $session) {
			Conferencer::add_admin_notice("Removed ".get_the_title($post_id)." from <a href='post.php?post=$session->ID&action=edit' target='_blank'>$session->post_title</a>.");
		}
	}

	function columns($columns) {
		$columns = parent::columns($columns);
		$columns['conferencer_session_keynote'] = "Keynote";
		$columns['conferencer_session_speakers'] = "Speakers";
		$columns['conferencer_session_track'] = "Track";
		$columns['conferencer_session_room'] = "Room";
		$columns['conferencer_session_time_slot'] = "Time Slot";
		$columns['conferencer_session_sponsors'] = "Sponsors";
		return $columns;
	}

	var $column_session_cache = array();
	function column($column) {
		parent::column($column);

		global $post;

		switch (str_replace('conferencer_session_', '', $column)) {
			case 'keynote':
				echo $post->keynote ? "keynote" : "";
				break;
			case 'speakers':
				$links = array();
				foreach (Conferencer::get_posts('speaker', $post->speakers) as $speaker) {
					$links[] =
						"<a href='post.php?action=edit&post=$speaker->ID'>".
						str_replace(' ', '&nbsp;', $speaker->post_title).
						"</a>";
				}

				echo implode(', ', $links);
				break;
			case 'sponsors':
				$links = array();
				foreach (Conferencer::get_posts('sponsor', $post->sponsors) as $sponsor) {
					$links[] =
						"<a href='post.php?action=edit&post=$sponsor->ID'>".
						str_replace(' ', '&nbsp;', $sponsor->post_title).
						"</a>";
				}

				echo implode(', ', $links);
				break;
			case 'track':
				if ($post->track) echo "<a href='post.php?action=edit&post=$post->track'>".get_the_title($post->track)."</a>";
				break;
			case 'room':
				if ($post->room) echo "<a href='post.php?action=edit&post=$post->room'>".get_the_title($post->room)."</a>";
				break;
			case 'time_slot':
				if ($post->time_slot) {
					$starts = floatVal(get_post_meta($post->time_slot, '_conferencer_starts', true));
					$ends = floatVal(get_post_meta($post->time_slot, '_conferencer_ends', true));

					echo "<a href='post.php?action=edit&post=$post->time_slot'>";
					echo date('n/j/y', $starts);
					echo '<br />';
					echo date('g:ia', $starts);
					if ($ends) echo '&mdash;'.date('g:ia', $ends);
					echo "</a>";
				}

				break;
		}
	}
}

endif; // class_exists check

new Conferencer_Session();