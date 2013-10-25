<?php

new Conferencer_Track();
class Conferencer_Track extends Conferencer_CustomPostType {
	var $slug = 'track';
	var $archive_slug = 'tracks';
	var $singular = "Track";
	var $plural = "Tracks";
	
	function columns($columns) {
		$columns = parent::columns($columns);
		$columns['conferencer_track_session_count'] = "Sessions";
		return $columns;
	}
}