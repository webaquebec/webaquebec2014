<?php

if( !class_exists('Conferencer_Track') ):

class Conferencer_Track extends Conferencer_CustomPostType {
	var $slug = 'track';
	var $archive_slug = 'tracks';
	var $singular = "Track";
	var $plural = "Tracks";
	var $menu_icon = "dashicons-editor-justify";

	function columns($columns) {
		$columns = parent::columns($columns);
		$columns['conferencer_track_session_count'] = "Sessions";
		return $columns;
	}
}

endif; // class_exists check

new Conferencer_Track();