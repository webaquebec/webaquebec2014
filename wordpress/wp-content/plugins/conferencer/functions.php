<?php

if (!function_exists('implode_with_serial_and')) {
	function implode_with_serial_and($args) {
		if (!is_array($args)) return '';
		if (count($args) == 1) return $args[0];
		
		$last = array_pop($args);
		return implode(', ', $args).', and '.$last;
	}
}

if (!function_exists('comma_separated_post_titles')) {
	function comma_separated_post_titles($posts, $link = true, $serial_and = true) {
		if (!is_array($posts)) return '';
		
		$items = array();
		$count = 0;
		foreach ($posts as $post) {
			$item = $post->post_title;
			if ($link) $item = "<a href='".get_permalink($post->ID)."'>$item</a>";
			if ($serial_and && ++$count > 1 && $count == count($posts)) $item = " and $item";
			$items[] = $item;
		}
		
		return implode(', ', $items);
	}
}

if (!function_exists('deep_empty')) {
	function deep_empty($var) {
		if (is_array($var)) {
			foreach ($var as $value) {
				if (!deep_empty($value)) return false;
			}
			return true;
		} else return empty($var);
	}
}

if (!function_exists('deep_trim')) {
	function deep_trim($var) {
		if (is_array($var)) {
			$array = array();
			foreach ($var as $key => $value) {
				$array[$key] = deep_trim($value);
			}
			return $array;
		} else return trim($var);
	}
}

if (!function_exists('output_classes')) {
	function output_classes($classes,$echo = true) {
		if (count($classes)) 
		  if($echo)
		    echo ' class="'.implode(' ', $classes).'"';
		  else
		    return ' class="'.implode(' ', $classes).'"';
	}
}

if (!function_exists('generate_excerpt')) {
	function generate_excerpt($post_id = false) {
		if ($post_id) $post = is_numeric($post_id) ? get_post($post_id) : $post_id;
		else $post = $GLOBALS['post'];

		if (!$post) return '';
		if (isset($post->post_excerpt) && !empty($post->post_excerpt)) return $post->post_excerpt;
		if (!isset($post->post_content)) return '';
	
		$content = $raw_content = $post->post_content;
	
		if (!empty($content)) {
			$content = strip_shortcodes($content);
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content);

			$excerpt_length = apply_filters('excerpt_length', 55);
			$words = preg_split("/[\n\r\t ]+/", $content, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				$content = implode(' ', $words);
				$content .= "...";
			} else $content = implode(' ', $words);
		}
	
		return apply_filters('wp_trim_excerpt', $content, $raw_content);
	}
}

if (!function_exists('debug')) {
	function debug($var) {
		echo "<pre style='background-color: #EEE; padding: 5px;'>";
		print_r($var);
		echo "</pre>";
	}
}

if (!function_exists('get_day')) {
	function get_day($timestamp) {
		$getdate = getdate($timestamp);
		return mktime(0, 0, 0, $getdate['mon'], $getdate['mday'], $getdate['year']);
	}
}