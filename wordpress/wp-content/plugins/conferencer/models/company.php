<?php

// TODO: add to page content, speakers for this company

if( !class_exists('Conferencer_Company') ):

class Conferencer_Company extends Conferencer_CustomPostType {
	var $slug = 'company';
	var $archive_slug = 'companies';
	var $singular = "Company";
	var $plural = "Companies";
	var $menu_icon = "dashicons-groups";
}

endif; // class_exists check

new Conferencer_Company();