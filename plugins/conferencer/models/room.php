<?php

new Conferencer_Room();
class Conferencer_Room extends Conferencer_CustomPostType {
	var $slug = 'room';
	var $archive_slug = 'rooms';
	var $singular = "Room";
	var $plural = "Rooms";
	
	function columns($columns) {
		$columns['conferencer_room_session_count'] = "Sessions";
		return parent::columns($columns);
	}
}