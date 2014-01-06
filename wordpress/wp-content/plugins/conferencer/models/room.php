<?php

if( !class_exists('Conferencer_Room') ):

class Conferencer_Room extends Conferencer_CustomPostType {
	var $slug = 'room';
	var $archive_slug = 'rooms';
	var $singular = "Room";
	var $plural = "Rooms";
	var $menu_icon = "dashicons-admin-home";

	function columns($columns) {
		$columns['conferencer_room_session_count'] = "Sessions";
		return parent::columns($columns);
	}
}

endif; // class_exists check

new Conferencer_Room();