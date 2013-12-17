<?php
/*
Template Name: Entête
*/
global $post;


/** Menu principal // Main menu **/
$page_args = array(
  	'posts_per_page'   => -1,
    'orderby'          => 'menu_order',
    'order'            => 'ASC',
    'meta_query'       => array(
      	                    array(
      	                        'key' => 'in_main_menu',
      	                        'value' => 1,
      	                        'compare' => '='
      	                    )
        	                ),
    
    'post_type' => 'page',
);

$pages = get_posts($page_args);
$main_menu_output = "";
foreach($pages as $page){
    $main_menu_output .= "<li>";
      $main_menu_output .= '<a itemprop="url" href="#'.$page->post_name.'">';
        $main_menu_output .= '<span itemprop="name">'.$page->post_title.'</span>';
      $main_menu_output .= "</a>";
    $main_menu_output .= "</li>";
}

/** Date de l'événement // Event date **/

$start_datetime_js = get_field('start_datetime', $post->ID);
$end_datetime_js = get_field('end_datetime', $post->ID);

$mtl_tz = new DateTimeZone('America/Montreal');

$start_datetime = new DateTime($start_datetime_js,$mtl_tz);

$end_datetime = new DateTime($end_datetime_js,$mtl_tz);

$start_unixtime = $start_datetime->format('U');
$end_unixtime = $end_datetime->format('U');

$start_time_text = "";
$end_time_text = "";

if(date("n",$start_unixtime) == date("n",$end_unixtime) && date("Y",$start_unixtime) == date("Y",$end_unixtime)){
  $start_time_text .= '<time itemprop="startDate" datetime="'.date("c",$start_unixtime).'">';
  $start_time_text .= date("j",$start_unixtime);
  $start_time_text .= '</time>';
}
else if(date("Y",$start_unixtime) == date("Y",$end_unixtime)){
    $start_time_text .= '<time itemprop="startDate" datetime="'.date("c",$start_unixtime).'">';
    $start_time_text .= date("j",$start_unixtime);
    $start_time_text .= '</time>';
    $start_time_text .= ' '.strftime("%B",$start_unixtime);
}
else{
    $start_time_text .= '<time itemprop="startDate" datetime="'.date("c",$start_unixtime).'">';
    $start_time_text .= date("j",$start_unixtime);
    $start_time_text .= '</time>';
    $start_time_text .= ' '.strftime("%B",$start_unixtime);
    $start_time_text .= ' '.date("Y",$start_unixtime);
}

$end_time_text .= '<time itemprop="endDate" datetime="'.date("c",$end_unixtime).'">';
$end_time_text .= date("j",$end_unixtime);
$end_time_text .= '</time>';
$end_time_text .= ' '.strftime("%B",$end_unixtime);
$end_time_text .= ' '.date("Y",$end_unixtime);

/** Compte à rebours // Countdown **/

$countdown_output = "";
$time_remaining = null;

/** Événement pas commencé // Event not yet started **/
if(time() <= $start_unixtime){
  $time_remaining = $start_datetime->diff(new DateTime());
  
  $hours_remaining = ($time_remaining->days*24)+$time_remaining->h;
  
  //$countdown_output .= '<span class="visuallyhidden">Il reste</span>';
  
  $countdown_output .= '<div class="event-stats-group days'.($time_remaining->days > 100 ? ' large' : '').' '.($time_remaining->days > 3 ? '' : 'visuallyhidden').'"><div class="stat days">';
      $countdown_output .= '<span class="stat-number">'.$time_remaining->days.'</span>';
      $countdown_output .= '<span class="stat-caption">jours</span>';
  $countdown_output .= '</div></div>';
  
  if($time_remaining->days > 3){
      $hours_remaining = $time_remaining->h;
  }
  
  $countdown_output .= '<div data-from-unix-time="'.$start_unixtime.'" class="event-stats-group hours"><div class="stat hours">';
      $countdown_output .= '<span class="stat-number">'.$hours_remaining.'</span>';
      $countdown_output .= '<span class="stat-caption">heures</span>';
  $countdown_output .= '</div></div>';
  
    //$countdown_output .= '<span class="visuallyhidden">et</span>';
  
  $countdown_output .= '<div class="event-stats-group minutes"><div class="stat minutes">';
      $countdown_output .= '<span class="stat-number">'.$time_remaining->i.'</span>';
      $countdown_output .= '<span class="stat-caption">minutes</span>';
  $countdown_output .= '</div></div>';
  

  $countdown_output .= '<span class="visuallyhidden">et</span>';
  $countdown_output .= '<div class="event-stats-group seconds '.($time_remaining->days > 3 ? 'visuallyhidden' : '').'"><div class="stat seconds">';
      $countdown_output .= '<span class="stat-number">'.$time_remaining->s.'</span>';
      $countdown_output .= '<span class="stat-caption">secondes</span>';
  $countdown_output .= '</div></div>';
  
  //$countdown_output .= '<span class="visuallyhidden">avant l\'événement du Web à Québec.</span>';
}
/** Événement est commencé // Event has started **/
else{

}

/** Conférenciers en vedette // Featured speakers **/

$feat_speakers_args = array(
  	'posts_per_page'   => 3,
  	'orderby'          => 'meta_value_num',
  	'order'            => 'ASC',
  	'meta_key'         => '_conferencer_order',
  	'meta_query'       => array(
      	                    array(
      	                        'key' => 'featured_speaker',
      	                        'value' => 1,
      	                        'compare' => '='
      	                    )
        	                ),
  	'post_type'        => 'speaker',
  	'post_status'      => 'publish',
  	'suppress_filters' => true );

$feat_speakers = get_posts( $feat_speakers_args );
$feat_speakers_output = "";

foreach($feat_speakers as $feat_speaker){

  $feat_speakers_sessions = Conferencer::get_sessions($feat_speaker->ID);
  if(!empty($feat_speakers_sessions)){
      $feat_speakers_sessions = array_shift($feat_speakers_sessions);
  }
  $feat_speakers_output .= (!empty($feat_speakers_sessions) ? '<a href="'. get_permalink($feat_speakers_sessions->ID).'">' : '<a href="">');
      
      $feat_speakers_output .= '<figure itemprop="performer" itemscope itemtype="http://schema.org/Person">';

          $feat_speakers_output .= '<figcaption>';
              $feat_speakers_output .= '<span class="name" itemprop="name">'.$feat_speaker->post_title.'</span>';
              $feat_speakers_output .= '<span class="job" itemprop="jobTitle">'.get_post_meta( $feat_speaker->ID, '_conferencer_title', true ).'</span>';
          $feat_speakers_output .= '</figcaption>';
          
          $thumb = get_post_thumbnail_id($feat_speaker->ID);
          $img_url = wp_get_attachment_url( $thumb,'full' );
          
          if(function_exists('aq_resize')){
            $image = aq_resize( $img_url, 227, 190, true );
            if(empty($image)){
              $image = aq_resize( $img_url, 227, 190, false );
              if(empty($image)){
                $image = $img_url;
              }
            }
            else{
              $image = $img_url;
            }
          }
          else{
            $image = $img_url;
          }
          
          if($image){
            $feat_speakers_output .= '<span class="img-crop"><img class="speaker-thumb" src="'.$image.'" alt="" itemprop="image" /></span>';
          }
      $feat_speakers_output .= '</figure>';
  $feat_speakers_output .= '</a>';
}


?>
<!-- Home section -->
<header class="home">
    <div class="l-section-wrapper">

        <a href="#" class="visuallyhidden focusable">Passer au contenu</a>

        <h1 itemprop="name" class="visuallyhidden">Le Web à Québec</h1>
        
        <div id="nav-main">
            <div class="snapmenu-wrapper">
    
                <div class="snapmenu-logo">
                    <a href="<?php echo get_bloginfo('url'); ?>">
                        <img src="<?php bloginfo('template_directory'); ?>/img/logo.png" alt="">
                    </a>
                </div>
    
                <div class="snapmenu-mobile-buttons">
                    <a href="#horaire">Horaire</a>
                    <button class="btn-toggle-menu">
                        <span class="visuallyhidden">Ouvrir le menu</span>
                    </button>
                </div>
    
                <nav role="navigation" class="nav-main-wrapper">
                    <ul itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">
                        <?php echo $main_menu_output; ?>
                    </ul>
                </nav>
    
                <a href="<?php echo get_field('eventbrite_link', $post->ID); ?>" class="event-subscribe event-subscribe-menu">Achetez vos billets</a>
            </div>
        </div>

        <div class="event-description">
            <p class="strong">Web à Québec</p>
            <p class="date">
                Du <?php echo $start_time_text ?>
                au <?php echo $end_time_text ?>
            </p>
            <p class="location" itemprop="location" itemscope itemtype="http://schema.org/Place">
                <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    <span itemprop="name">Espace 400<sup>e</sup> Bell</span>,
                    <span itemprop="addressRegion">Québec</span>
                </span>
            </p> 
        </div>

        <div class="event-stats">
            <?php echo $countdown_output; ?>
            <div class="event-stats-group conference">
                <div class="stat conference">
                    <span class="stat-number"><?php echo get_field('nb_sessions', $post->ID); ?></span>
                    <span class="stat-caption">conférences</span>
                </div>
            </div>
            <div class="event-stats-group workshop">
                <div class="stat workshop">
                    <span class="stat-number"><?php echo get_field('nb_workshops', $post->ID); ?></span>
                    <span class="stat-caption">workshops</span>
                </div>
            </div>
            <div class="event-stats-group days-total">
                <div class="stat days-total">
                    <span class="stat-number"><?php echo get_field('nb_days', $post->ID); ?></span>
                    <span class="stat-caption">jours</span>
                </div>
            </div>
            <div class="event-stats-group city">
                <div class="stat city">
                    <span class="stat-number">1</span>
                    <span class="stat-caption">ville</span>
                </div>
            </div>
        </div>

        <div class="event-subscribe event-subscribe-big">
          <a href="<?php echo get_field('eventbrite_link', $post->ID); ?>">
              <span>Achetez</span>
              <span>vos billets</span>
              <span>dès maintenant</span>
              <img src="<?php bloginfo('template_directory'); ?>/img/logo-eventbrite.png" alt="">
          </a>
        </div>

        <div class="event-speakers">
            <?php echo $feat_speakers_output;  ?>
        </div>

    </div>
        
    <video id="video_background" poster="<?php bloginfo('template_directory'); ?>/img/video-poster.png" preload="auto" autoplay="true" loop="loop" muted="muted" volume="0">
        <source src="<?php bloginfo('template_directory'); ?>/video/loop.webm" type="video/webm">
        <source src="<?php bloginfo('template_directory'); ?>/video/loop.mp4" type="video/mp4">
        Video not supported
    </video>
</header><!-- end of Home section -->