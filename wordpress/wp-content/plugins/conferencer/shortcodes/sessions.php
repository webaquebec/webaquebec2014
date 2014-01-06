<?php

if( !class_exists('Conferencer_Shortcode_Sesssions') ):

class Conferencer_Shortcode_Sesssions extends Conferencer_Shortcode {
	var $shortcode = 'sessions';
	var $defaults = array(
		'post_id' => false,
		'post_ids' => false,
		'title' => false,
		'no_sessions_message' => "There aren't any sessions for this yet.",
		'link_post' => true,
		'link_sessions' => true,
		'title_tag' => 'h3',
	);

	var $buttons = array('sessions');

	static $post_types_with_sessions = array('speaker', 'room', 'time_slot', 'track', 'sponsor');

	function add_to_page($content) {
		if (in_array(get_post_type(), self::$post_types_with_sessions)) {
			$content .= do_shortcode('[sessions]');
		}
		return $content;
	}

	function prep_options() {
		// Turn csv into array
		if (!is_array($this->options['post_ids'])) $this->options['post_ids'] = array();
		if (!empty($this->options['post_ids'])) $this->options['post_ids'] = explode(',', $this->options['post_ids']);

		// add post_id to post_ids and get rid of it
		if ($this->options['post_id']) $this->options['post_ids'] = array_merge($this->options['post_ids'], explode(',', $this->options['post_id']));
		unset($this->options['post_id']);

		// fallback to current post if nothing specified
		if (empty($this->options['post_ids']) && $GLOBALS['post']->ID) $this->options['post_ids'] = array($GLOBALS['post']->ID);

		// unique list
		$this->options['post_ids'] = array_unique($this->options['post_ids']);
	}

	function content() {
		extract($this->options);

		$errors = array();

		if (empty($post_ids)) $errors[] = "No posts ID provided";

		foreach ($post_ids as $post_id) {
			$post = get_post($post_id);

			if (!$post) {
				$errors[] = "$post_id is not a valid post ID";
			} else if (!in_array(get_post_type($post), self::$post_types_with_sessions)) {
				$errors[] = "<a href='".get_permalink($post->ID)."'>$post->post_title</a> is not the correct type of post";
			}
		}

		if (count($errors)) return "[Shortcode errors (sessions): ".implode(', ', $errors)."]";

		$sessions = Conferencer::get_sessions($post_ids);

		ob_start();

		if (!empty($sessions) || !empty($no_sessions_message)) { ?>

			<div class="session-list">
				<<?php echo $title_tag; ?>>
					<?php if (!$title) { ?>
						Sessions for
						<?php
							$titles = array();
							foreach ($post_ids as $post_id) {
								$html = get_the_title($post_id);
								if ($link_post) $html = "<a href='".get_permalink($post_id)."'>$html</a>";
								$titles[] = $html;
							}
							echo implode_with_serial_and($titles);
						?>
					<?php } else echo $title; ?>
				</<?php echo $title_tag; ?>>

				<?php if (empty($sessions)) { ?>
					<p><?php echo $no_sessions_message; ?></p>
				<?php } else { ?>
					<ul>
						<?php foreach ($sessions as $session) { ?>
							<li>
								<?php
									$html = $session->post_title;
									if ($link_sessions) $html = "<a href='".get_permalink($session->ID)."'>$html</a>";
									echo $html;
								?>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>

		<?php }

		return ob_get_clean();
	}
}

endif; // class_exists check

new Conferencer_Shortcode_Sesssions();