<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ): the_post();

  	$time_slot_id = get_post_meta(get_the_ID(), '_conferencer_time_slot', true);
  	$room_id = get_post_meta(get_the_ID(), '_conferencer_room', true);
  	$speakers_ids = get_post_meta(get_the_ID(), '_conferencer_speakers', true);
  	$english_session = get_post_meta(get_the_ID(), 'english', true);
  	$session_lists = get_field('lists',get_the_ID());
  	$related_sessions_ids = get_post_meta(get_the_ID(), 'related_sessions', true);
  	$themes = wp_get_post_terms(get_the_ID(), 'theme');

  	$speakers_output = "";
  	$speaker_name = "";
  	if(count($speakers_ids) > 1){
      	$speaker_name = "Panel";

      	foreach ($speakers_ids as $speaker_id) {
      	  $speaker_post = get_post(array_shift($speakers_ids));

      	  $speakers_output .= '<h2><span class="small">À propos de </span><span class="big">'.$speaker_post->post_title.'</span></h2>';

      	  $speakers_output .= '<div class="content">'.(get_post_field('post_excerpt', $speaker_post->ID) ? apply_filters('the_content', get_post_field('post_excerpt', $speaker_post->ID)) : apply_filters('the_content', get_post_field('post_content', $speaker_post->ID))).'</div>';


      	  $speakers_output .= '<ul class="social">';
      	  $speaker_website = addhttp(get_post_meta($speaker_post->ID, 'website', true));
      	  $speaker_website_clean = rtrim(str_replace('www.', '', str_replace('https://', '', str_replace('http://', '', $speaker_website))), '/');
      	  if(!empty($speaker_website) && $speaker_website != 'http://' ){
      	    $speakers_output .= '<li class="website"><a href="'.$speaker_website.'">'.$speaker_website_clean.'</a></li>';
      	  }

      	  $speaker_twitter_handle = str_replace('@', '', get_post_meta($speaker_post->ID, 'twitter_handle', true));
      	  if(!empty($speaker_twitter_handle)){
      	    $speakers_output .= '<li class="twitter"><a href="http://twitter.com/'.$speaker_twitter_handle.'">@'.$speaker_twitter_handle.'</a></li>';
      	  }
      	  $speakers_output .= '</ul>';

      	  unset($speaker_post);
      	}
  	}
  	else if(count($speakers_ids) == 1){
  	  $speaker_post = get_post(array_shift($speakers_ids));
  	  $speaker_name = $speaker_post->post_title;

  	  $speakers_output .= '<h2><span class="small">À propos de </span><span class="big">'.$speaker_post->post_title.'</span></h2>';

      $thumb = get_post_thumbnail_id($speaker_post->ID);
      $img_url = wp_get_attachment_url( $thumb,'full' );

      if(function_exists('aq_resize')){
        $image = aq_resize( $img_url, 227, 190, true );
        if(empty($image)){
          $image = aq_resize( $img_url, 227, 190, false );
          if(empty($image)){
            $image = $img_url;
          }
        }
      }
      else{
        $image = $img_url;
      }

      if($image){
        $speakers_output .= '<span class="img-crop"><img class="speaker-thumb" src="'.$image.'" alt="" itemprop="image" /></span>';
      }

  	  $speakers_output .= '<div class="content">'.(get_post_field('post_excerpt', $speaker_post->ID) ? apply_filters('the_content', get_post_field('post_excerpt', $speaker_post->ID)) : apply_filters('the_content', get_post_field('post_content', $speaker_post->ID))).'</div>';

  	  $speakers_output .= '<ul class="social">';

  	  $speaker_website = addhttp(get_post_meta($speaker_post->ID, 'website', true));
  	  $speaker_website_clean = rtrim(str_replace('www.', '', str_replace('https://', '', str_replace('http://', '', $speaker_website))), '/');
  	  if(!empty($speaker_website) && $speaker_website != 'http://' ){
  	    $speakers_output .= '<li class="website"><a href="'.$speaker_website.'">'.$speaker_website_clean.'</a></li>';
  	  }

      $speaker_twitter_handle = str_replace('@', '', get_post_meta($speaker_post->ID, 'twitter_handle', true));
      if(!empty($speaker_twitter_handle)){
        $speakers_output .= '<li class="twitter"><a href="http://twitter.com/'.$speaker_twitter_handle.'">@'.$speaker_twitter_handle.'</a></li>';
      }

      $speaker_linkedin_handle = get_post_meta($speaker_post->ID, 'linkedin_handle', true);
      if(!empty($speaker_linkedin_handle)){
        $speakers_output .= '<li class="linkedin"><a href="http://www.linkedin.com/in/'.$speaker_linkedin_handle.'">'.$speaker_linkedin_handle.'</a></li>';
      }
  	  $speakers_output .= '</ul>';
  	  unset($speaker_post);
  	}

  	$related_sessions_output = "";
  	if(!empty($related_sessions_ids)){
  	  $related_sessions_output .= "<h2>Ces conférences pourraient vous intéresser : </h2>";
  	  $related_sessions_output .= '<ul class="subevents">';
    	foreach ($related_sessions_ids as $related_session_id) {
    	  $related_session_post = get_post($related_session_id);
    	  $related_sessions_output .= '<li itemprop="subEvent" itemscope itemtype="http://schema.org/Event">';
    	  $related_sessions_output .= '<h3 class="session-title" itemprop="name"><a href="'.get_permalink($related_session_id).'">'.$related_session_post->post_title.'</a></h3>';

    	  $related_session_speakers = get_post_meta($related_session_post->ID, '_conferencer_speakers', true);
    	  if(count($related_session_speakers) > 1){
    	    $related_sessions_output .= '<span class="session-speaker">
    	        <span itemprop="name">'."Panel".'</span></span>';
    	  }
    	  else if(count($related_session_speakers) == 1){
    	    $related_sessions_output .= '<span class="session-speaker" itemprop="performer" itemscope="" itemtype="http://schema.org/Person">
    	        <span itemprop="name">'.get_the_title(array_shift($related_session_speakers)).'</span></span>';
    	  }

    	  $related_session_starts = get_post_meta($time_slot_id, '_conferencer_starts', true);
    	  $related_session_ends = get_post_meta($time_slot_id, '_conferencer_ends', true);
    	  $related_sessions_output .= '<span class="session-meta">';
    	      $related_sessions_output .= '<span class="session-date"><time datetime="'.trim(strftime("%Y-%m-%d",$related_session_starts)).'">'.strftime("%A %e %B",$related_session_starts).'</time></span>'."\n";
    	      $related_sessions_output .= '<span class="session-time-slot">'."\n";
    	          $related_sessions_output .= '<time itemprop="startDate" datetime="'.trim(strftime("%Y-%m-%d %H:%M",$related_session_starts)).'">'.trim(strftime("%k:%M",$related_session_starts)).'</time> à';
    	          $related_sessions_output .= '<time itemprop="endDate" datetime="'.trim(strftime("%Y-%m-%d %H:%M",$related_session_ends)).'">'.trim(strftime("%k:%M",$related_session_ends)).'</time>';
    	      $related_sessions_output .= '</span>'."\n";
    	      $related_sessions_output .= '<span class="session-room" itemprop="location" itemscope itemtype="http://schema.org/Place">';
    	          $related_sessions_output .= '<span itemprop="address" itemscope itemtype="http://schema.org/Place">'."\n";
    	              $related_sessions_output .= '<span itemprop="name">'.get_the_title(get_post_meta($related_session_post->ID, '_conferencer_room', true)).'</span>';
    	          $related_sessions_output .= '</span>';
    	      $related_sessions_output .= '</span>';
    	  $related_sessions_output .= '</span>';

    	  $related_sessions_output .= '</li>';
    	  unset($related_session_post);
    	}
  	  $related_sessions_output .= "</ul>";
  	}

  	$themes_output = "";
  	if(!empty($themes)){
  	  $themes_output .= "<h2>Thématiques de cette conférence</h2>";
    	$themes_output .= '<ul class="tags">';
    	foreach ($themes as $theme) {
    	  $themes_output .= '<li><span>'.$theme->name.'</span></li>';
    	}
  		$themes_output .= "</ul>";
  	}

  	$session_start_unix = get_post_meta($time_slot_id, '_conferencer_starts', true);
  	$session_ends_unix = get_post_meta($time_slot_id, '_conferencer_ends', true);
	  $session_room = get_the_title($room_id);
	  $lists_output = "";
	  if(!empty($session_lists)){
  	  foreach ($session_lists as $list) {
  	    $lists_output .= "<h2>".$list['title']."</h2>";
  	    if(!empty($list['elements'])){
  	      $lists_output .= '<ul class="questions">';
    	    foreach ($list['elements'] as $element) {
    	      $lists_output .= "<li>".$element['text']."</li>";
    	    }
  	      $lists_output .= "</ul>";
  	    }
  	  }
	  }
	?>
  <!-- Page content section -->
  <section id="content" class="page-content">
      <div class="l-section-wrapper">

          <div class="l-row">
              <div class="l-col">

                  <article>

                      <header id="contenu" class="content-header go-to-content">
                          <h1 itemprop="name"><?php the_title() ?></h1>
                          <div class="informations">
                              <span class="session-speaker" itemprop="performer" itemscope itemtype="http://schema.org/Person">
                                  <span itemprop="name"><?php echo $speaker_name; ?></span>
                              </span>
                              <span class="session-meta">
                                  <span class="date"><time datetime="<?php echo trim(strftime("%Y-%m-%d",$session_start_unix)); ?>"><?php echo strftime("%A %e %B",$session_start_unix); ?></time></span>
                                  <span class="time">
                                      <time itemprop="startDate" datetime="<?php echo trim(strftime("%Y-%m-%d %H:%M",$session_start_unix)); ?>"><?php echo trim(strftime("%k h %M",$session_start_unix)); ?></time> à
                                      <time itemprop="endDate" datetime="<?php echo trim(strftime("%Y-%m-%d %H:%M",$session_ends_unix)); ?>"><?php echo trim(strftime("%k h %M",$session_ends_unix)); ?></time>
                                  </span>
                                  <span class="location" itemprop="location" itemscope itemtype="http://schema.org/Place">
                                      <span itemprop="address" itemscope itemtype="http://schema.org/Place">
                                          <span itemprop="name"><?php echo $session_room; ?></span>
                                      </span>
                                  </span>
                              </span>
                              <?php if($english_session): ?>
                                <p class="language">La conférence sera donnée en anglais.</p>
                              <?php endif; ?>
                          </div>
                      </header>

                      <div class="content">
                          <?php the_content(); ?>
                          <?php echo $lists_output; ?>
                          <?php echo $themes_output; ?>
                      </div>

                  </article>

                  <div  class="back-link"><a href="<?php echo get_bloginfo('url'); ?>">Retour à l'horaire</a></div>

              </div>
              <div class="l-col sidebar">

                  <aside class="highlight" role="complementary">
                        <?php echo $speakers_output; ?>
                  </aside>

                    <?php echo $related_sessions_output; ?>

              </div>
          </div>

      </div>
  </section><!-- end of sponsors section -->

	<?php endwhile; ?>
<?php else: ?>
	<p><?php _e( 'Aucun résultat' ); ?></p>
<?php endif; ?>
<?php

$page_args = array(
    'post_type' => 'page',
  	'meta_key' => '_wp_page_template',
  	'meta_value' => 'home-footer.php'
);

$pages = get_posts($page_args);
global $post;

foreach($pages as $post){
    setup_postdata( $post );
    $template_name = str_replace('.php', '', get_post_meta( $post->ID, '_wp_page_template', true ));
    $template_name = explode('-',$template_name);
    get_template_part($template_name[0],$template_name[1]);
}


get_footer(); ?>
