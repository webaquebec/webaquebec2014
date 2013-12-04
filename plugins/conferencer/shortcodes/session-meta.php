<?php

new Conferencer_Shortcode_Session_Meta();
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

		$meta = array();
		foreach (explode(',', $show) as $type) {
			$type = trim($type);
			switch ($type) {
				case 'session':
				case 'double-session':
				case 'keynote':
				
				  $type_rendered = $type;			
				
				  $html = '<td class="session';
				  
				  $terms = wp_get_post_terms($post->ID, 'theme', array("fields" => "slugs"));
				  
				  if(!empty($terms)){
  				  $html .= ' '.implode(' ', $terms);
				  }
				  
				  $starts = get_post_meta($post->time_slot, '_conferencer_starts', true);
				  $ends = get_post_meta($post->time_slot, '_conferencer_ends', true);
				  $duration = $ends-$starts;
				  
				  if($type == 'keynote' && !$post->room && ($duration < 2000)){
				    $html .= ' break small';
				    $type_rendered = 'break';
				  }
				  else if($type == 'keynote' && !$post->room){
				    $html .= ' break';
				    $type_rendered = 'break';
				  }
				  else if($type == 'keynote'){
				    $html .= ' keynote';
				  }
				  
			  	if ($post->room) {
			  	  $room = get_post($post->room);
			  	  $html .= ' '.$room->post_name;
			  	}
			  	
			  	if (count($speakers = Conferencer::get_posts('speaker', $post->speakers))) {
			  	  if(count($speakers) > 1){
			  	    $html .= ' panel';
			  	  }
			  	  else if(count($speakers) == 1){
			  	    $speaker = array_shift($speakers);
			  	      
			  	    $thumb = get_post_thumbnail_id($speaker->ID);
			  	    $img_url = wp_get_attachment_url( $thumb,'full' );
			  	    
			  	    if(!empty($img_url)){
			  	      $html .= ' speaker-thumb';
			  	    }
			  	  }
			  	}
		  	  else{
		  	    $html .= ' no-speaker';
		  	  }
			  	
			  	$html .= '"';
			  	
			  	if ($type == 'double-session') {
			  	  $html .= ' rowspan="2"';
			  	}
			  	
			  	if ($type == 'keynote') {
			  	  $html .= ' colspan="'.$colspan.'"';
			  	}
			  	
			  	$html .= '>';
			  	
			  	$html .= '<div class="session-content" itemprop="subEvent" itemscope itemtype="http://schema.org/Event">';
		  	  $html .= '<div class="session-content-wrapper">';
			  	
					$meta[] = $html;
					break;
					
				case 'title':
					$html = $post->post_title;
					if ($link_title) $html = "<a href='".get_permalink($post->ID)."'>$html</a>";
					$meta[] = '<span class="session-title" itemprop="name">'.$title_prefix.$html.$title_suffix."</span>";
					break;
				
				case 'time':
					if ($post->time_slot && $post->room) {
						$starts = get_post_meta($post->time_slot, '_conferencer_starts', true);
						$ends = get_post_meta($post->time_slot, '_conferencer_ends', true);
						//$html = date($date_format, $starts).", ".date($time_format, $starts).$time_separator.date($time_format, $ends);
						$html = "";
						$html .= '<span class="session-time-slot" itemprop="subEvent">';
  						$html .= '<span><time itemprop="startDate" datetime="'.date("c",$starts).'">'.date("G:i",$starts).'</time></span> à ';
  						$html .= '<span><time itemprop="endDate" datetime="'.date("c",$ends).'">'.date("G:i",$ends).'</time></span>';
						$html .= '</span>';
						$meta[] = $time_prefix.$html.$time_suffix;
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
						$meta[] = $speakers_prefix.$html.$speaker_suffix;
					}
					break;
					
				case 'speakers_w_photos':
					if (count($speakers = Conferencer::get_posts('speaker', $post->speakers))) {
					  $html = "";
					  if(count($speakers) > 1){
					    $html .= '<span class="session-description" itemprop="description">Panel</span>';
					  }
					  else{
					    $speaker = array_shift($speakers);
						    
					    $thumb = get_post_thumbnail_id($speaker->ID);
					    $img_url = wp_get_attachment_url( $thumb,'full' );
					    
					    if(function_exists('aq_resize')){
					      $image = aq_resize( $img_url, 187, 160, true );
					    }
					    else{
					      $image = $img_url;
					    }
						  
						  if($image){
  					    $html .= '<figure class="session-speaker" itemprop="performer" itemscope itemtype="http://schema.org/Person">';
  						    $html .= '<figcaption><span itemprop="name">'.$speaker->post_title.'</span></figcaption>';
  						    $html .= '<img class="session-speaker-thumb" src="'.$image.'" itemprop="image">';
  					    $html .= '</figure>';
					    }
					    else{
  					    $html .= '<span class="session-speaker" itemprop="performer" itemscope itemtype="http://schema.org/Person">';
  					      $html .= '<span itemprop="name">'.$speaker->post_title.'</span>';
  					    $html .= '</span>';
					    }
					  }
						$meta[] = $speakers_prefix.$html.$speaker_suffix;
					}
					break;

				case 'room':
					if ($post->room) {
						$html = get_the_title($post->room);
						if ($link_room) $html = "<a href='".get_permalink($post->room)."'><span itemprop='name'>$html</span></a>";
						$meta[] = '<span class="session-room" itemprop="location" itemscope itemtype="http://schema.org/Place"><span itemprop="name">'.$room_prefix.$html.$room_suffix."</span></span>";
					}
					break;

				case 'track':
					if ($post->track) {
						$html = get_the_title($post->track);
						if ($link_track) $html = "<a href='".get_permalink($post->track)."'>$html</a>";
						$meta[] = "<span class='track'>".$track_prefix.$html.$track_suffix."</span>";
					}
					break;

				case 'sponsors':
					if (count($sponsors = Conferencer::get_posts('sponsor', $post->sponsors))) {
						$html = comma_separated_post_titles($sponsors, $link_sponsors);
						$meta[] = "<span class='sponsors'>".$sponsors_prefix.$html.$sponsors_suffix."</span>";
					}
					break;
					
				default:
					$meta[] = "Unknown session attribute";
			}
		}
		
		$output = implode("", $meta);
		
		$output .= '<button class="session-bookmark"><span class="visuallyhidden">Ajouter cette conférence à mon horaire</span></button>';
	  $output .= '</div>';
		
		$output .= '</div></td>';

		return $output;
	}
}