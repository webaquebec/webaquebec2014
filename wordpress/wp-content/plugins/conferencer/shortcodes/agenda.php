<?php

/* ============================================================================

	You can override the session display function in your own template.
	In your own functions.php, define:
		conferencer_agenda_display_session($session, $options)

============================================================================ */

if( !class_exists('Conferencer_Shortcode_Agenda') ):

class Conferencer_Shortcode_Agenda extends Conferencer_Shortcode {
	var $shortcode = 'agenda';
	var $defaults = array(
		'column_type' => 'track',
		'session_tooltips' => false,
		'show_empty_rows' => true,
		'show_empty_columns' => true,
		'show_empty_cells' => true,
		'show_unassigned_column' => false,
		'tabs' => 'days',
		'tab_day_format' => 'M. j, Y',
		'tab_day_strf' => '%A %e %B',
		'tab_day_dt_strf' => '%Y-%m-%d',
		'row_day_format' => 'l, F j, Y',
		'row_time_format' => 'G:i',
		'show_row_ends' => false,
		'keynote_spans_tracks' => true,
		'link_sessions' => true,
		'link_speakers' => false,
		'link_rooms' => false,
		'link_time_slots' => false,
		'link_columns' => false,
		'unassigned_column_header_text' => 'N/A',
		'unscheduled_row_text' => 'Unscheduled',
		'mobile' => false,
	);

	var $buttons = array('agenda');

	function prep_options() {
		parent::prep_options();

		if (!in_array($this->options['column_type'], array('track', 'room'))) {
			$this->options['column_type'] = false;
		}

		if ($this->options['show_empty_cells'] != null) {
			$this->options['show_empty_rows'] = $this->options['show_empty_cells'];
			$this->options['show_empty_columns'] = $this->options['show_empty_cells'];
		}
	}

	function content() {
		extract($this->options);
		$conferencer_options = get_option('conferencer_options');

		// Define main agenda variable

		$agenda = array();

		// Fill agenda with empty time slot rows

		foreach (Conferencer::get_posts('time_slot', false, 'start_time_sort') as $time_slot_id => $time_slot) {
		  $starts = get_post_meta($time_slot_id, '_conferencer_starts', true);
			$agenda[$starts][$time_slot_id] = array();
		}
		$agenda[0] = array(); // for unscheduled time slots

		// If the agenda is split into columns, fill rows with empty "cell" arrays

		if ($column_type) {
			$column_post_counts = array(
				-1 => 0, // keynotes
				0 => 0, // unscheduled
			);
			$column_posts = Conferencer::get_posts($column_type);

		  foreach($agenda as $time => $time_slots){
  			foreach ($time_slots as $time_slot_id => $time_slot) {
  				foreach ($column_posts as $column_post_id => $column_post) {
  					$column_post_counts[$column_post_id] = 0;
  					$agenda[$time][$time_slot_id][$column_post_id] = array();
  				}
  				$agenda[$time][$time_slot_id][0] = array();
  			}
			}
		}

		// Get all session information

		$sessions = Conferencer::get_posts('session', false, 'title_sort');
		foreach (array_keys($sessions) as $id) {
			Conferencer::add_meta($sessions[$id]);
		}

		// Put sessions into agenda variable

		foreach ($sessions as $session) {
			$time_slot_id = $session->time_slot ? $session->time_slot : 0;
		  $starts = get_post_meta($time_slot_id, '_conferencer_starts', true);

			if ($column_type) {
				$column_id = $session->$column_type ? $session->$column_type : 0;
				if ($keynote_spans_tracks && $session->keynote) $column_id = -1;
				$agenda[$starts][$time_slot_id][$column_id][$session->ID] = $session;
				$column_post_counts[$column_id]++;
			} else {
				$agenda[$starts][$time_slot_id][$session->ID] = $session;
			}
		}

		// Remove empty unscheduled rows

		if (deep_empty($agenda[0])) unset($agenda[0]);

		// Conditionally remove empty rows and columns

		if (!$show_empty_rows) {
			foreach ($agenda as $time => $time_slots) {
  			foreach ($time_slots as $time_slot_id => $cells) {
  				$non_session = get_post_meta($time_slot_id, '_conferencer_non_session', true);
  				if (!$non_session && deep_empty($cells)) unset($agenda[$time_slot_id]);
  			}
  		}
		}

		if (!$show_empty_columns) {
			$empty_column_post_ids = array();
			foreach ($column_posts as $column_post_id => $column_post) {
				if (!$column_post_counts[$column_post_id]) $empty_column_post_ids[] = $column_post_id;
			}

			foreach ($agenda as $time => $time_slots) {
  			foreach ($time_slots as $time_slot_id => $cells) {
  				foreach ($empty_column_post_ids as $empty_column_post_id) {
  					unset($agenda[$time_slot_id][$empty_column_post_id]);
  				}
  			}
			}
		}

		// Set up tabs

		if ($tabs) {
			$tab_headers = array();

			foreach ($agenda as $time => $time_slots) {
  			foreach ($time_slots as $time_slot_id => $cells) {
  				if ($tabs == 'days') {
  					if ($starts = get_post_meta($time_slot_id, '_conferencer_starts', true)) {
  						$tab_headers[] = get_day($starts);
  					} else $tab_headers[] = 0;
  				}
				}
			}

			$tab_headers = array_unique($tab_headers);

			if (count($tab_headers) < 2) $tabs = false;
		}

		// Set up column headers

		if ($column_type) {
			$column_headers = array();

			// post column headers
			foreach ($column_posts as $column_post) {
				if (!$show_empty_columns && in_array($column_post->ID, $empty_column_post_ids)) continue;

				$column_headers[] = array(
					'title' => $column_post->post_title,
					'class' => 'column_'.$column_post->post_name,
					'link' => $link_columns ? get_permalink($column_post->ID) : false,
				);
			}

			if ($show_unassigned_column && count($column_post_counts[0])) {
				// extra column header for sessions not assigned to a column
				$column_headers[] = array(
					'title' => $unassigned_column_header_text,
					'class' => 'column_not_applicable',
					'link' => false,
				);
			} else {
				// remove cells if no un-assigned sessions
				foreach ($agenda as $time => $time_slots) {
  				foreach ($time_slots as $time_slot_id => $cells) {
  					unset($agenda[$time_slot_id][0]);
  				}
				}
			}
		}

		// Remove unscheduled time slot, if without sessions
		//if (deep_empty($agenda[0])) unset($agenda[0]);

		// Start buffering output

		ob_start();

		//echo '<pre>'; var_dump($agenda); echo '</pre>';

		$output = "";

		$output .= '<div class="schedule-wrapper">';

			/*if (isset($conferencer_options['details_toggle']) && $conferencer_options['details_toggle']) {
					$output .= '<a href="#" class="conferencer_session_detail_toggle">';
						$output .= '<span class="show">display session details</span>';
						$output .= '<span class="hide">hide session details</span>';
					$output .= '</a>';
			}*/

      /** Days buttons // Buttons jours **/
			if ($tabs) {
					$output .= '<header class="days-buttons">';
					foreach ($tab_headers as $tab_header) {
							if ($tabs == 'days') {
									$output .= '<button>';
									if($tab_header){
									  $output .= '<time datetime="'.strftime($tab_day_dt_strf, $tab_header).'"><span>'.strftime($tab_day_strf, $tab_header).'</span></time>';
									}
									$output .= '</button>';
							}
					}
					$output .= '</header><div class="js-slider"><div class="js-slider-container">';
			} else {
			    $output .= '<div class="js-slider"><div class="js-slider-container"><div class="day-wrapper slide">';
					$output .= '<table class="grid">';
					if ($column_type)
					  $output .= $this->display_headers($column_headers);
					$output .= '<tbody>';
			}

			$row_starts = $last_row_starts = $second_table = false;
      $currentDayTab = -1;
      $rowspan_nosession = array();

			foreach ($agenda as $time => $time_slots) {

				  $total_cells = array();
				  $fake_slot_id = null;
				  $number_of_time_slots = 0;
				  foreach ($time_slots as $time_slot_id => $cells) {
					  //$total_cells = array_merge($total_cells,$cells);
					  foreach($cells as $key => $value){
					    $total_cells[$key][] = $value;
					  }
					  $fake_slot_id = $time_slot_id;
					  $number_of_time_slots++;
				  }
				  $cells = $total_cells;
				  $time_slot_id = $fake_slot_id;



				  //echo '<pre>'; var_dump($cells); echo '</pre>';
					// Set up row information

					$last_row_starts = $row_starts;
					$row_starts = get_post_meta($time_slot_id, '_conferencer_starts', true);
					$row_ends = get_post_meta($time_slot_id, '_conferencer_ends', true);
					$non_session = get_post_meta($time_slot_id, '_conferencer_non_session', true);
					$no_sessions = deep_empty($cells);

					// Show day seperators
					//$show_next_day = $row_day_format !== false && date('', $row_starts) != date('w', $last_row_starts);
					$show_next_day =  $row_day_format !== false && $currentDayTab == date('z', $row_starts) ? false : true;
					$currentDayTab = date('z', $row_starts);

					if ($show_next_day) {
						  if ($tabs) {
                  if ($second_table) {
  									$output .= '</tbody>';
    								$output .= '</table>';
    								$output .= '</div>';
							    }
							    else
							      $second_table = true;

							  //$output .= '<div id="conferencer_agenda_tab_'.get_day($row_starts).'">';
							  $output .= '<div class="day-wrapper slide">';
  							  $output .= '<table class="grid">';
  							  $output .= '<caption>Événements pour le <time datetime="'.strftime($tab_day_dt_strf, $row_starts).'"><span>'.lcfirst(strftime($tab_day_strf, $row_starts)).'</span></time></caption>';
  								if ($column_type)
  								  $output .= $this->display_headers($column_headers);
  								$output .= '<tbody>';
						  }
						  else {
							  $output .= '<tr class="day">';
								$output .= '<td colspan="'.($column_type ? count($column_headers) + 1 : 2).'">';
								$output .= $row_starts ? date($row_day_format, $row_starts) : $unscheduled_row_text;
								$output .= '</td>';
  							$output .= '</tr>';
              }
          }
					// Set row classes

					$classes = array();
					if ($non_session) $classes[] = 'non-session';
					else if ($no_sessions) $classes[] = 'no-sessions';

				  $output .= '<tr'.output_classes($classes,false).'>';

					// Time slot column --------------------------

					$output .= '<td class="time-slot">';
					if ($time_slot_id) {
						$time_slot_link = get_post_meta($time_slot_id, '_conferencer_link', true)
							OR $time_slot_link = get_permalink($time_slot_id);
						$html = date($row_time_format, $row_starts);
						if ($show_row_ends) $html .= '<span class="time-slot-end"> à '.date($row_time_format, $row_ends) . '</span>';
						if ($link_time_slots) $html = "<a href='$time_slot_link'>$html</a>";
						$output .= $html;
					}
					$output .= '</td>';

					// Display session cells ---------------------

					$colspan = $column_type ? count($column_headers) : 1;

					if ($non_session) { // display a non-sessioned time slot
					  $output .= '<td class="session" colspan="'.$colspan.'"><p>';
						  $html = get_the_title($time_slot_id);
						  if ($link_time_slots)
						    $html = "<a href='$time_slot_link'>$html</a>";
						  $output .= $html;
						$output .= '</p></td>';

					}
					else if (isset($cells[-1])) {
					  //$output .= '<td class="session keynote" colspan="'.$colspan.'">';
					  foreach ($cells[-1] as $sessions) {
					  	foreach ($sessions as $session) {
					  	  $session->colspan = $colspan;
					  		$output .= $this->display_session($session,'keynote,title,speakers_w_photos,room,time');
					  	}
					  }
					  //$output .= '</td>';
					}
					else if ($column_type) { // if split into columns, multiple cells

            $smallest_duration = 999999999999;
            foreach ($cells as $cell_sessions) {
              foreach ($cell_sessions as $sessions) {
                foreach ($sessions as $key => $session) {
                  $starts = get_post_meta($session->time_slot, '_conferencer_starts', true);
                  $ends = get_post_meta($session->time_slot, '_conferencer_ends', true);
                  $duration = $ends-$starts;

                  if($duration < $smallest_duration){
                    $smallest_duration = $duration;
                  }
                }
              }
            }

            foreach ($cells as $track => $cell_sessions) {

          		if(isset($rowspan_nosession[$track]) && $rowspan_nosession[$track] != 0){
          		  $rowspan_nosession[$track] = $rowspan_nosession[$track]-1;
          		}

              if(!empty($cell_sessions)){

                $no_sessions = true;

                foreach ($cell_sessions as $sessions) {
                  if(!empty($sessions)){

                    foreach ($sessions as $key => $session) {

                      $no_sessions = false;

                      $time_slot_id = $session->time_slot ? $session->time_slot : 0;
                      $starts = get_post_meta($time_slot_id, '_conferencer_starts', true);
                      $ends = get_post_meta($time_slot_id, '_conferencer_ends', true);
                      $duration = $ends-$starts;

                      if($duration > $smallest_duration){
                        $rowspan_calc = intval(ceil($duration/$smallest_duration));
                        $rowspan_nosession[$track] = $rowspan_calc;
                        $session->rowspan = $rowspan_calc;
                      }
                      else{
                        $rowspan_nosession[$track] = 0;
                      }
                      //$output .= '<td class="session' . (empty($cell_sessions) ? ' no-sessions':'') . '" '.(($ends-$starts) > 3600 ? ' rowspan="2"' : '').'>';

                      $output .= $this->display_session($session,(empty($cell_sessions) ? 'no-session,':'session,').'title,speakers,room,time');

                      //$output .= '</td>';
                    }
                  }
                }

                if($no_sessions && $track > 0 && empty($rowspan_nosession[$track])){
                  $output .= '<td class="empty"></td>';
                }
              }
            }


					}
					else {
					  $output .= '<td class="session ' . (empty($cells) ? 'no-sessions':'') . '">';
					  foreach ($cells as $sessions) {
					  	foreach ($sessions as $session) {
					  		$output .= $this->display_session($session);
					  	}
					  }
					  $output .= '</td>';
					}
				$output .= '</tr>';
			}
		$output .= '</tbody>';
	$output .= '</table>';

			if ($tabs) {
				$output .= '</div>';
			}

		$output .= '</div></div></div>';

		// Retrieve and return buffer

	  echo $output;

		return ob_get_clean();
	}

	function display_headers($column_headers) {
	  $output = "";
		$output .= '<thead>';
			$output .= '<tr>';
				$output .= '<th id="vide" class="column-time-slot"></th>';
				foreach ($column_headers as $column_header) {
					$output .= '<th class="'.$column_header['class'].'">';
					$html = $column_header['title'];
					if ($column_header['link']) $html = "<a href='".$column_header['link']."'>$html</a>";
					$output .= $html;
					$output .= '</th>';
				}
			$output .= '</tr>';
		$output .= '</thead>';

		return $output;
	}

	function display_session($session,$show = 'title,speakers' ) {
		if (function_exists('conferencer_agenda_display_session')) {
			conferencer_agenda_display_session($session, $this->options);
			return;
		}
		extract($this->options);
    $output = "";
    /*echo '<br><br>'; var_dump("
    		[session_meta
    			post_id='{$session->ID}'
    			show='".$show."'
    			link_title=".($link_sessions ? 'true' : 'false')."
    			link_speakers=".($link_speakers ? 'true' : 'false')."
    			link_room=".($link_rooms ? 'true' : 'false')."
    			colspan='".($session->colspan ? $session->colspan : 0)."'
    			rowspan='".($session->rowspan ? $session->rowspan : 0)."'
    		]
    	");*/
		$output .= do_shortcode("
				[session_meta
					post_id='{$session->ID}'
					show='".$show."'
					link_title=".($link_sessions ? 'true' : 'false')."
					link_speakers=".($link_speakers ? 'true' : 'false')."
					link_room=".($link_rooms ? 'true' : 'false')."
					colspan='".($session->colspan ? $session->colspan : 0)."'
					rowspan='".($session->rowspan ? $session->rowspan : 0)."'
				]
			");

			if ($session_tooltips) {
				$output .= '<div class="session-tooltip">'. do_shortcode("
						[session_meta
							post_id='{$session->ID}'
							show='title,speakers,room'
							link_all=false
						]
					");

					$output .= '<p class="excerpt">'.generate_excerpt($session).'</p>';
					$output .= '<div class="arrow"></div><div class="inner-arrow"></div>';
				$output .= '</div>';
			 }

    return $output;
	 }

}

endif; // class_exists check

new Conferencer_Shortcode_Agenda();