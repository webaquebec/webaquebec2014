<?php

add_action('after_setup_theme', array('Conferencer_Sponsors_Widget', 'add_image_sizes'));
add_action('widgets_init', array('Conferencer_Sponsors_Widget', 'init'));

class Conferencer_Sponsors_Widget extends WP_Widget {
	function init() {
		register_widget('Conferencer_Sponsors_Widget');
	}
	
	function add_image_sizes() {
		foreach (get_option('conferencer_sponsors_widget_image_sizes', array()) as $id => $size) {
			add_image_size(
				"sponsors_widget_$id",
				$size['width'],
				$size['height']
			);
		}
	}
	
	function Conferencer_Sponsors_Widget() {
		parent::WP_Widget(false, $name = "Sponsors Slideshow");
	}
	
	function widget($args, $instance) {
		global $wp_query;
		
		extract($args);
		
		$levels = Conferencer::get_posts('sponsor_level');
		foreach ($levels as $id => $level) {
			$levels[$id]->sponsors = array();
		}
		
		foreach (Conferencer::get_posts('sponsor') as $sponsor) {
			Conferencer::add_meta($sponsor);
			$levels[$sponsor->level]->sponsors[$sponsor->ID] = $sponsor;
		}

		foreach ($levels as $id => $level) {
			shuffle($levels[$id]->sponsors);
		}
		
		$title = apply_filters(
			'widget_title',
			empty($instance['title']) ? 'Sponsors' : $instance['title'],
			$instance,
			$this->id_base
		);

		echo $before_widget.$before_title.$title.$after_title;
		foreach ($levels as $level) { ?>
			<?php if (count($level->sponsors)) { ?>
				<div class="sponsor_level sponsor_<?php echo $level->post_name; ?>">
					<h4><?php echo $level->post_title; ?></h4>
					<div class="sponsors">
						<?php foreach ($level->sponsors as $sponsor) { ?>
							<div class="sponsor">
								<?php
									$html = $sponsor->post_title;
									
									if (has_post_thumbnail($sponsor->ID)) {
										$html = get_the_post_thumbnail(
											$sponsor->ID,
											"sponsors_widget_$sponsor->level",
											array(
												'alt' => $sponsor->post_title,
												'title' => $sponsor->post_title,
											)
										);
									}
									
									if (!empty($sponsor->url)) $html = "<a href='$sponsor->url' target='_blank'>$html</a>";
									
									echo $html;
								?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		<?php } // foreach
		
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		$image_sizes = array();
		if (is_array($_POST['width'])) {
			foreach ($_POST['width'] as $id => $width) {
				$image_sizes[$id] = array(
					'width' => $_POST['width'][$id],
					'height' => $_POST['height'][$id],
				);
			}
		}		
		update_option('conferencer_sponsors_widget_image_sizes', $image_sizes);

		return $instance;
	}
	
	function form($instance) { ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input
				type="text"
				class="widefat"
				id="<?php echo $this->get_field_id('title'); ?>"
				name="<?php echo $this->get_field_name('title'); ?>"
				value="<?php echo esc_attr($instance['title']); ?>"
			/>
		</p>
		
		<?php
			$sponsor_levels = new WP_Query(array(
				'post_type' => 'sponsor_level',
				'posts_per_page' => -1, // get all
			));
			
			$levels = $sponsor_levels->posts;
			uasort($levels, array('Conferencer', 'order_sort'));
			
			$image_sizes = get_option('conferencer_sponsors_widget_image_sizes', array());
		?>
		
		<?php if (count($levels)) { ?>
		
			<label>Logo sizes:</label>
			<table>
				<tr>
					<th>level</th>
					<th>width</th>
					<th>height</th>
				</tr>
				<?php foreach ($levels as $level) { ?>
					<tr>
						<td><?php echo $level->post_title; ?></td>
						<td>
							<input
								type="text"
								size="4"
								class="conferencer_widget_logo_size"
								name="width[<?php echo $level->ID; ?>]"
								value="<?php if (array_key_exists($level->ID, $image_sizes)) echo $image_sizes[$level->ID]['width']; ?>"
							/>
						</td>
						<td>
							<input
								type="text"
								size="4"
								class="conferencer_widget_logo_size"
								name="height[<?php echo $level->ID; ?>]"
								value="<?php if (array_key_exists($level->ID, $image_sizes)) echo $image_sizes[$level->ID]['height']; ?>"
							/>
						</td>
					</tr>
				<?php } ?>
			</table>
		
		<?php } else { ?>
			
			<p>Once you define some sponsor levels, you can set their logo size for this widget here.</p>
			
		<?php } ?>
		
	<?php }
}