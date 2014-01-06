<?php

if( !class_exists('Conferencer_Shortcode_Speaker_Meta') ):

class Conferencer_Shortcode_Speaker_Meta extends Conferencer_Shortcode {
	var $shortcode = 'speaker_meta';
	var $defaults = array(
		'post_id' => false,

		'show' => "title,company",

		'speaker_prefix' => "",
		'title_prefix' => "",
		'company_prefix' => "",

		'speaker_suffix' => "",
		'title_suffix' => "",
		'company_suffix' => "",

		'link_all' => true,
		'link_company' => true,
		'link_title' => true,
		'link_speaker' => true
	);

	var $buttons = array('speaker_meta');

	function add_to_page($content) {
		if (get_post_type() == 'speaker') {
			$meta = function_exists('conferencer_speaker_meta')
					? conferencer_speaker_meta($post)
					: do_shortcode('[speaker_meta]');
			$content = $meta.$content;
		}
		return $content;
	}

	function prep_options() {
		parent::prep_options();

		if (!$this->options['post_id'] && isset($GLOBALS['post'])) {
			$this->options['post_id'] = $GLOBALS['post']->ID;
		}

		if ($this->options['link_all'] === false) {
			$this->options['link_title'] = false;
			$this->options['link_speaker'] = false;
			$this->options['link_company'] = false;
		}
	}

	function content() {
		extract($this->options);

		$post = get_post($post_id);
		if (!$post) return "[Shortcode error (speaker_meta): Invalid post_id.  If not used within a speaker page, you must provide a speaker ID using 'post_id'.]";
		if ($post->post_type != 'speaker') {
			if ($post_id) return "[Shortcode error (speaker_meta): <a href='".get_permalink($post_id)."'>$post->post_title</a> (ID: $post_id, type: $post->post_type) is not a speaker.]";
			else return "[Shortcode error (speaker_meta): This post is not a speaker.  Maybe you meant to supply a speaker using post_id.]";
		}

		Conferencer::add_meta($post);

		$meta = array();
		foreach (explode(',', $show) as $type) {
			$type = trim($type);

			switch ($type) {
				case 'title':
					$html = $post->title;
					if ($link_title) $html = "<a href='".get_permalink($post->ID)."'>$html</a>";
					$meta[] = "<span class='title'>".$title_prefix.$html.$title_suffix."</span>";
					break;

				case 'company':
					if ($post->company) {
						$html = get_the_title($post->company);
						if ($link_company) $html = "<a href='".get_permalink($post->company)."'>$html</a>";
						$meta[] = "<span class='company'>".$company_prefix.$html.$company_suffix."</span>";
					}
					break;

				case 'speaker':
					$html = $post->post_title;
					if ($link_speaker) $html = "<a href='".get_permalink($post->ID)."'>$html</a>";
					$meta[] = "<span class='speaker'>".$speaker_prefix.$html.$speaker_suffix."</span>";
					break;

				default:
					$meta[] = "Unknown speaker attribute";
			}
		}

		return count($meta) ? "<p class='speaker_meta'>".implode("<br />", $meta)."</p>" : '';
	}
}

endif; // class_exists check

new Conferencer_Shortcode_Speaker_Meta();