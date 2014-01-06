<?php

if( !class_exists('Conferencer_Shortcode_Session_Meta') ):

class Conferencer_Shortcode_Session_Meta extends Conferencer_Shortcode {
	var $shortcode = 'session_meta';
	var $defaults = array(
		'post_id' => false,

		'show' => "time,speakers,room,track,sponsors",

		'title_prefix' => "",
		'time_prefix' => "",
		'speakers_prefix' => "",
		'room_prefix' => "",
		'track_prefix' => "",
		'sponsors_prefix' => "",

		'title_suffix' => "",
		'time_suffix' => "",
		'speaker_suffix' => "",
		'room_suffix' => "",
		'track_suffix' => "",
		'sponsor_suffix' => "",

		'date_format' => 'l, F j, Y',
		'time_format' => 'g:ia',
		'time_separator' => ' &ndash; ',

		'link_all' => true,
		'link_title' => true,
		'link_speakers' => true,
		'link_room' => true,
		'link_track' => true,
		'link_sponsors' => true,

		'rowspan' => 1,
		'colspan' => 1
	);

	var $buttons = array('session_meta');

	function add_to_page($content) {
		if (get_post_type() == 'session') {
			$meta = function_exists('conferencer_session_meta')
					? conferencer_session_meta($post)
					: do_shortcode('[session_meta]');
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
			$this->options['link_speakers'] = false;
			$this->options['link_room'] = false;
			$this->options['link_track'] = false;
			$this->options['link_sponsors'] = false;
		}
	}

	function content() {
		extract($this->options);

		$type_rendered = "session";

		$post = get_post($post_id);
		if (!$post) return "[Shortcode error (session_meta): Invalid post_id.  If not used within a session page, you must provide a session ID using 'post_id'.]";
		if ($post->post_type != 'session') {
			if ($post_id) return "[Shortcode error (session_meta): <a href='".get_permalink($post_id)."'>$post->post_title</a> (ID: $post_id, type: $post->post_type) is not a session.]";
			else return "[Shortcode error (session_meta): This post is not a session.  Maybe you meant to supply a session using post_id.]";
		}

		Conferencer::add_meta($post);


		$output = $output_session = $output_content = "";

    $user_sessions = get_user_sessions();
    $user_session_selected = false;
    if(in_array($post->ID, $user_sessions)){
      $user_session_selected = true;
    }

		foreach (explode(',', $show) as $type) {
			$type = trim($type);
			switch ($type) {
				case 'session':
				case 'double-session':
				case 'keynote':

				  $type_rendered = $type;

				  $output .= '<td data-session-id="'.$post->ID.'"';

				  if($type == 'keynote'){
				    $output .= ' headers="vide"';
				  }

				  $output .= ' class="session';

				  $terms = wp_get_post_terms($post->ID, 'theme', array("fields" => "slugs"));

				  $output .= ' filter-all';
				  if(!empty($terms)){
  				  $output .= ' filter-'.implode(' filter-', $terms);
				  }

				  if($user_session_selected){
				    $output .= ' bookmarked';
				  }

				  $starts = get_post_meta($post->time_slot, '_conferencer_starts', true);
				  $ends = get_post_meta($post->time_slot, '_conferencer_ends', true);
				  $duration = $ends-$starts;

				  if($type == 'keynote' && !$post->room && ($duration < 2000)){
				    $output .= ' break small';
				    $type_rendered = 'break';
				  }
				  else if($type == 'keynote' && !$post->room){
				    $output .= ' break';
				    $type_rendered = 'break';
				  }
				  else if($type == 'keynote'){
				    $output .= ' keynote';
				  }

			  	if ($post->room) {
			  	  $room = get_post($post->room);
			  	  $output .= ' '.$room->post_name;
			  	}

			  	if (count($speakers = Conferencer::get_posts('speaker', $post->speakers))) {
			  	  if(count($speakers) > 1){
			  	    $output .= ' panel';
			  	  }
			  	  else if(count($speakers) == 1){
			  	    $speaker = array_shift($speakers);

			  	    $thumb = get_post_thumbnail_id($speaker->ID);
			  	    $img_url = wp_get_attachment_url( $thumb,'full' );

			  	    if(!empty($img_url)){
			  	      $output .= ' speaker-thumb';
			  	    }
			  	  }
			  	}
		  	  else{
		  	    $output .= ' no-speaker';
		  	  }

			  	$output .= '"';

			  	if ($type == 'double-session' || !empty($rowspan)) {
			  	  if(empty($rowspan))
			  	    $rowspan = 2;
			  	  $output .= ' rowspan="'.$rowspan.'"';
			  	}

			  	if ($type == 'keynote') {
			  	  $output .= ' colspan="'.$colspan.'"';
			  	}

			  	if ($post->room){
			  	  $output .= ' itemprop="subEvent" itemscope itemtype="http://schema.org/Event"';
			  	}

			  	$output .= '>';
					break;

				case 'title':
					$html = $post->post_title;
					if ($link_title && $type_rendered != 'break') $html = "<a href='".get_permalink($post->ID)."'>$html</a>";
					$output_content .= '<span class="session-title" itemprop="name">'.$title_prefix.$html.$title_suffix."</span>";
					break;

				case 'time':
					if ($post->time_slot && $type_rendered == 'keynote') {
						$starts = get_post_meta($post->time_slot, '_conferencer_starts', true);
						$ends = get_post_meta($post->time_slot, '_conferencer_ends', true);
						//$html = date($date_format, $starts).", ".date($time_format, $starts).$time_separator.date($time_format, $ends);
						$html = "";
						$html .= '<span class="session-time-slot" itemprop="subEvent">';
  						$html .= '<span><time itemprop="startDate" datetime="'.date("c",$starts).'">'.date("G:i",$starts).'</time></span> à ';
  						$html .= '<span><time itemprop="endDate" datetime="'.date("c",$ends).'">'.date("G:i",$ends).'</time></span>';
						$html .= '</span>';
						$output_content .= $time_prefix.$html.$time_suffix;
					}
					else{
						$starts = get_post_meta($post->time_slot, '_conferencer_starts', true);
						$ends = get_post_meta($post->time_slot, '_conferencer_ends', true);
						$date_a = new DateTime();
						$date_a->setTimestamp($starts);
						$date_b = new DateTime();
						$date_b->setTimestamp($ends);



						$interval = $date_a->diff($date_b);
						//$html = date($date_format, $starts).", ".date($time_format, $starts).$time_separator.date($time_format, $ends);
						$html = "";
						$html .= '<span class="visuallyhidden">Durée : ';

						if($interval->h > 0){
					        $html .= $interval->h.' heure'.($interval->h>1?'s':'').' ';
						}

						if($interval->i > 0){
						    $html .= $interval->i.' minute'.($interval->i>1?'s':'');
						}

						$html .= '</span>';
						$output_content .= $time_prefix.$html.$time_suffix;
					}
					break;

				case 'speakers':
					if (count($speakers = Conferencer::get_posts('speaker', $post->speakers))) {
					  $html = "";
					  if(count($speakers) > 1){
					    $html .= '<span class="session-description" itemprop="description">Panel</span>';
					  }
					  else{
					    $speaker = array_shift($speakers);
					    $html .= '<span class="session-speaker" itemprop="performer" itemscope itemtype="http://schema.org/Person">';
  					    $html .= '<span itemprop="name">'.$speaker->post_title.'</span>';
					    $html .= '</span>';
					  }
						$output_content .= $speakers_prefix.$html.$speaker_suffix;
					}
					break;

				case 'speakers_w_photos':
					if (count($speakers = Conferencer::get_posts('speaker', $post->speakers))) {
					  $html = "";
					  if(count($speakers) > 1){
					    $output_content .= '<span class="session-description" itemprop="description">Panel</span>';
					  }
					  else{
					    $speaker = array_shift($speakers);

					    $thumb = get_post_thumbnail_id($speaker->ID);
					    $img_url = wp_get_attachment_url( $thumb,'full' );

					    if(function_exists('aq_resize')){
					      $image = aq_resize( $img_url, 187, 160, true );
					      if(!$image){
					        $image = aq_resize( $img_url, 187, 160, false );
  					      if(!$image){
  					        $image = $img_url;
  					      }
					      }
					    }
					    else{
					      $image = $img_url;
					    }

						  if($image){
  					    $output_session .= '<span class="session-speaker-thumb"><!-- position: absolute -->';
  						    $output_session .= '<img src="'.$image.'" alt="">';
  					    $output_session .= '</span>';
					    }

					    $output_content .= '<span class="session-speaker" itemprop="performer" itemscope itemtype="http://schema.org/Person">';
					      $output_content .= '<span itemprop="name">'.$speaker->post_title.'</span>';
					    $output_content .= '</span>';
					  }
						$meta[] = $speakers_prefix.$html.$speaker_suffix;
					}
					break;

				case 'room':
					if ($post->room) {
						$html = get_the_title($post->room);
						if ($link_room) $html = "<a href='".get_permalink($post->room)."'><span itemprop='name'>$html</span></a>";
						$output_content .= '<span class="session-room" itemprop="location" itemscope itemtype="http://schema.org/Place"><span itemprop="name">'.$room_prefix.$html.$room_suffix."</span></span>";
					}
					break;

				case 'track':
					if ($post->track) {
						$html = get_the_title($post->track);
						if ($link_track) $html = "<a href='".get_permalink($post->track)."'>$html</a>";
						$output_content .= "<span class='track'>".$track_prefix.$html.$track_suffix."</span>";
					}
					break;

				case 'sponsors':
					if (count($sponsors = Conferencer::get_posts('sponsor', $post->sponsors))) {
						$html = comma_separated_post_titles($sponsors, $link_sponsors);
						$output_content .= "<span class='sponsors'>".$sponsors_prefix.$html.$sponsors_suffix."</span>";
					}
					break;

				default:
					//$meta[] = "Unknown session attribute";
			}
		}


    $output .= '<div class="session-wrapper"><!-- position: relative -->';
    $output .= '<div class="session-content"><!-- table-cell -->
    	<div class="session-content-wrapper"><!-- position: relative -->';
    $output .= $output_content;
    $output .= '</div></div>';
    $output .= $output_session;

    if($user_session_selected){
      $output .= '<button class="session-bookmark remove"><span class="visuallyhidden">Retirer cette conférence à mon horaire</span></button>';
    }
    else{
		  $output .= '<button class="session-bookmark add"><span class="visuallyhidden">Ajouter cette conférence à mon horaire</span></button>';
	  }

	  $output .= '</div></td>';

		return $output;
	}
}

endif; // class_exists check

new Conferencer_Shortcode_Session_Meta();