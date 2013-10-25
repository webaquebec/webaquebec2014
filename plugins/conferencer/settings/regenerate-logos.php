<?php

new Conferencer_Rengerate_Logos();
class Conferencer_Rengerate_Logos {
	function __construct() {
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('admin_notices', array(&$this, 'admin_notice'));
		add_action('wp_ajax_conferencer_logo_regenerate', array(&$this, 'ajax_logo_regenerate'));
		add_action('wp_ajax_conferencer_logo_regeneration_needed', array(&$this, 'ajax_logo_regeneration_needed'));
		add_action('wp_ajax_conferencer_logo_regeneration_done', array(&$this, 'ajax_logo_regeneration_done'));
	}
	
	function admin_menu() {
		$page = add_submenu_page(
			'conferencer',
			"Regenerate Logos",
			"Regenerate Logos",
			'edit_posts',
			'conferencer_regenerate-logos',
			array(&$this, 'page')
		);
		
		add_action("admin_print_styles-$page", array(&$this, 'includes'));
	}
	
	function includes() {
		wp_enqueue_script('conferencer-regenerate-logos');
	}
	
	function admin_notice() { ?>
	    <div id="conferencer_logo_regeneration_needed" class="updated<?php if (!get_option('conferencer_logo_regeneration_needed')) echo " closed"; ?>">
			<p>You've changed the logo sizes.  You'll need to <a href="<?php echo admin_url('admin.php?page=conferencer_regenerate-logos'); ?>">regenerate the logos</a>.</p>
		</div>
	<?php }
	
	function page() {
		if (!current_user_can('edit_posts')) wp_die("You do not have sufficient permissions to access this page.");

		$query = new WP_Query(array(
			'post_type' => 'sponsor',
			'posts_per_page' => -1, // get all
		));
		
		$ids = array();
		foreach ($query->posts as $sponsor) $ids[] = $sponsor->ID;
		
		?>
		
		<div id="conferencer_regenerate_logos" class="wrap">
			<h2>Regenerate Logos</h2>
			
			<p>When you upload images to WordPress, the system generates several versions of the image at different sizes.  These images are then used in various places in the site.  Conferencer encourages you to upload featured images for conference post types.  These can be used in your templates, for example to show head shots for speaker, or logos for companies.  Depending on your theme, certain sizes are specified for WordPress to generate, like a 75x75 image for thumbnails or a 120x80 image for an archive post image.  Conferencer allows you to define image sizes for a <a href="<?php echo admin_url('widgets.php'); ?>">sponsor slideshow widget</a>.  The problem is that WordPress only generates files when you upload them.  So if change the slideshow image sizes, then any logos previously uploaded do not have the correctly sized image file generated.</p>
			<p>This page allows you to regenerate your images based on the current image size settings.  So, if you have made recent changes to size settings, you'll need to regenerate your images now.  Don't worry, if you do not run this program, WordPress will still show an image.  It just might not be the size you want until you regenerate.</p>
			
			<?php if (count($ids)) { ?>
				
				<p class="submit"><input type="submit" class="button-primary" name="conferencer_regenerate_logos" id="conferencer_regenerate_logos_button" value="Regenerate" /></p>
				<div id="conferencer_regenerate_logos_progress"></div>
				<ul id="conferencer_regenerate_logos_console"><!-- JS --></ul>
				
				<script> conferencer_logo_regeneration_ids = [<?php echo implode(',', $ids); ?>]; </script>
				
			<?php } else { ?>
				
				<p>You'll need to create some sponsors before you can resize their logos.</p>
				
			<?php } ?>
				
		</div>
		
		<?php
	}
	
	static $regenerate_logo_error = false;
	
	function regenerate_logo($sponsor = false) {
		self::$regenerate_logo_error = false;
		
		if (!current_user_can('manage_options')) {
			self::$regenerate_logo_error = "incorrect permissions";
			return false;
		}

		if (!$sponsor) $sponsor = get_post($GLOBALS['post']);
		if (is_numeric($sponsor)) $sponsor = get_post($sponsor);
		
		if (!$sponsor || 'sponsor' != $sponsor->post_type) {
			self::$regenerate_logo_error = "invalid sponsor";
			return false;
		}
		
		$thumbID = get_post_thumbnail_id($sponsor->ID);
		
		if (!$thumbID) {
			self::$regenerate_logo_error = "no thumbnail";
			return false;
		}

		$path = get_attached_file($thumbID);
		if (false === $path || !file_exists($path)) {
			self::$regenerate_logo_error = "original cannot be found.";
			return false;
		}

		$metadata = wp_generate_attachment_metadata($thumbID, $path);
		
		if (is_wp_error($metadata)) {
			self::$regenerate_logo_error = $metadata->get_error_message();
			return false;
		}
		
		if (empty($metadata)) {
			self::$regenerate_logo_error = "unknown error";
			return false;
		}

		// If this fails, then it just means that nothing was changed (old value == new value)
		if (!wp_update_attachment_metadata($thumbID, $metadata)) {
			self::$regenerate_logo_error = "no change";
			return false;
		}

		return true;
	}
	
	function ajax_logo_regenerate() {
		@error_reporting(0); // Don't break the JSON result
		@set_time_limit(900); // 5 minutes
		header('Content-type: application/json');
		$titleLink = "<a href='".admin_url('post.php?action=edit&post='.$_REQUEST['id'])."'>".get_the_title($_REQUEST['id'])."</a>: ";
		die(json_encode(
			self::regenerate_logo($_REQUEST['id'])
				? array('success' => $titleLink." successfully resized")
				: array('error' => $titleLink.self::$regenerate_logo_error)
		));
	}

	function ajax_logo_regeneration_needed() {
		@error_reporting(0); // Don't break the JSON result
		@set_time_limit(900); // 5 minutes
		update_option('conferencer_logo_regeneration_needed', true);
		header('Content-type: application/json');
		die(json_encode(true));
	}

	function ajax_logo_regeneration_done() {
		@error_reporting(0); // Don't break the JSON result
		@set_time_limit(900); // 5 minutes
		update_option('conferencer_logo_regeneration_needed', false);
		header('Content-type: application/json');
		die(json_encode(true));
	}	
}