<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ): the_post();
	
  	$time_slot_id = get_post_meta(get_the_ID(), '_conferencer_time_slot', true);
  	$room_id = get_post_meta(get_the_ID(), '_conferencer_room', true);
  	$speakers_ids = get_post_meta(get_the_ID(), '_conferencer_speakers', true);
  	$english_session = get_post_meta(get_the_ID(), 'english', true);
  	$related_sessions_ids = get_post_meta(get_the_ID(), 'related_sessions', true);
  	$themes = wp_get_post_terms(get_the_ID(), 'theme');
  	
  	$speakers_output = "";
  	$speaker_name = "";
  	if(count($speakers_ids) > 1){
      	$speaker_name = "Panel";
      	
      	foreach ($speakers_ids as $speaker_id) {
      	  $speaker_post = get_post(array_shift($speakers_ids));
      	  
      	  $speakers_output .= '<h2><span>À propos de </span>'.$speaker_name.'</h2>';
      	  
      	  $speakers_output .= (get_post_field('post_excerpt', $speaker_post->ID) ? get_post_field('post_excerpt', $speaker_post->ID) : get_post_field('post_content', $speaker_post->ID));
      	  
      	  $speaker_twitter_handle = str_replace('@', '', get_post_meta($speaker_post->ID, 'twitter_handle', true));
      	  $speakers_output .= '<p><a href="http://twitter.com/'.$speaker_twitter_handle.'">@'.$speaker_twitter_handle.'</a></p>';
      	  
      	  $speaker_website = addhttp(get_post_meta($speaker_post->ID, 'website', true));
      	  $speaker_website_clean = rtrim(str_replace('www.', '', str_replace('https://', '', str_replace('http://', '', $speaker_website))), '/');
      	  $speakers_output .= '<p><a href="'.$speaker_website.'">'.$speaker_website_clean.'</a></p>';
      	  unset($speaker_post);
      	}
  	}
  	else if(count($speakers_ids) == 1){
  	  $speaker_post = get_post(array_shift($speakers_ids));
  	  $speaker_name = $speaker_post->post_title;
  	  
  	  $speakers_output .= '<h2><span>À propos de </span>'.$speaker_name.'</h2>';
  	  
  	  $speakers_output .= get_post_field('post_content', $speaker_post->ID);
  	  
  	  $speaker_twitter_handle = str_replace('@', '', get_post_meta($speaker_post->ID, 'twitter_handle', true));
  	  $speakers_output .= '<p><a href="http://twitter.com/'.$speaker_twitter_handle.'">@'.$speaker_twitter_handle.'</a></p>';
  	  
  	  $speaker_website = addhttp(get_post_meta($speaker_post->ID, 'website', true));
  	  $speaker_website_clean = rtrim(str_replace('www.', '', str_replace('https://', '', str_replace('http://', '', $speaker_website))), '/');
  	  $speakers_output .= '<p><a href="'.$speaker_website.'">'.$speaker_website_clean.'</a></p>';
  	  unset($speaker_post);
  	}
  	
  	$related_sessions_output = "";
  	if(!empty($related_sessions_ids)){
  	  $related_sessions_output .= "<h2>Ces conférences pourraient vous intéresser : </h2>";
  	  $related_sessions_output .= "<ul>";
    	foreach ($related_sessions_ids as $related_session_id) {
    	  $related_session_post = get_post($related_session_id);
    	  $related_sessions_output .= '<li>';
    	  $related_sessions_output .= '<h3><a href="'.get_permalink($related_session_id).'">'.$related_session_post->post_title.'</a></h3>';
    	  
    	  $related_session_speakers = get_post_meta($related_session_post->ID, '_conferencer_speakers', true);
    	  if(count($related_session_speakers) > 1){
    	    $related_sessions_output .= '<p>'."Panel".'</p>';
    	  }
    	  else if(count($related_session_speakers) == 1){
    	    $related_sessions_output .= '<p>'.get_the_title(array_shift($related_session_speakers)).'</p>';
    	  }
    	  
    	  $related_session['starts'] = get_post_meta($time_slot_id, '_conferencer_starts', true);
    	  $related_session['ends'] = get_post_meta($time_slot_id, '_conferencer_ends', true);
    	  $related_sessions_output .= '<p><span>'.strftime("%A %e %B",get_post_meta($time_slot_id, '_conferencer_starts', true)).'</span> / ';
    	  $related_sessions_output .= '<span>'.strftime("%k h %M",get_post_meta($time_slot_id, '_conferencer_starts', true)).' à '.strftime("%k h %M",get_post_meta($time_slot_id, '_conferencer_ends', true)).'</span> / ';
    	  $related_sessions_output .= '<span>'.get_the_title(get_post_meta($related_session_post->ID, '_conferencer_room', true)).'</span></p>';
    	  
    	  $related_sessions_output .= '</li>';
    	  unset($related_session_post);
    	}
  	  $related_sessions_output .= "</ul>";
  	}
  	
  	$themes_output = "";
  	if(!empty($themes)){
    	$themes_output .= "<ul>";
    	foreach ($themes as $theme) {
    	  $themes_output .= '<li>'.$theme->name.'</li>';
    	}
  		$themes_output .= "</ul>";
  	}
  	
  	$session_start_unix = get_post_meta($time_slot_id, '_conferencer_starts', true);
  	$session_ends_unix = get_post_meta($time_slot_id, '_conferencer_ends', true);
	  $session_room = get_the_title($room_id);
	?>
  
  <h1><?php the_title() ?></h1>
  <h2><?php echo $speaker_name; ?></h2>
  <p>
    <span><?php echo strftime("%A %e %B",$session_start_unix); ?></span> / 
    <span><?php echo strftime("%k h %M",$session_start_unix); ?> à <?php echo strftime("%k h %M",$session_ends_unix); ?></span> / 
    <span><?php echo $session_room; ?></span>
  </p>
  
  <?php if($english_session): ?>
    <p>La conférence sera donnée en anglais</p>
  <?php endif; ?>
  
  <?php the_content(); ?>
  
  <?php echo $themes_output; ?>
  
  <p><a href="<?php echo get_bloginfo('url'); ?>">Retour à l'horaire</a></p>
  
  <?php echo $speakers_output; ?>
  
  <?php echo $related_sessions_output; ?>

	<?php endwhile; ?>
<?php else: ?>
	<p><?php _e( 'Aucun résultat' ); ?></p>
<?php endif; ?>
<?php get_footer(); ?>
