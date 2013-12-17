<?php
/*
Template Name: À propos
*/
global $post;

$organizers_groups_args = array(
  	'hide_empty' => 0 );

$organizers_groups = get_terms( 'categorypersons' , $organizers_groups_args );

$organizers_output = "";
$volunteers_exclude = array();
foreach($organizers_groups as $organizers_group){
  $organizers_output .= '<section class="organizers-group">';
  $organizers_output .= '<header><h3>'.$organizers_group->name.'</h3></header>';
  $volunteers_exclude[] = $organizers_group->slug;
  
  $organizers_args = array(
    	'posts_per_page'   => -1,
    	'orderby'          => 'menu_order',
    	'order'            => 'ASC',
    	'post_type'        => 'person',
    	'post_status'      => 'publish',
    	'categorypersons'  => $organizers_group->slug,
    	'suppress_filters' => true );
    	
  $organizers = get_posts( $organizers_args );
  
  if(!empty($organizers)){
    $organizers_output .= '<ul class="organizers-list">';
    foreach($organizers as $organizer){
      $organizers_output .= '<li itemscope itemtype="http://schema.org/Person">';
      
          $thumb = get_post_thumbnail_id($organizer->ID);
          $img_url = wp_get_attachment_url( $thumb,'full' );
              
          if(function_exists('aq_resize')){
            $image = aq_resize( $img_url, '51', '51', true );
            if(!$image){
              $image = $img_url;
            }
          }
          else{
            $image = $img_url;
          }
      
          $organizers_output .= '<figure itemscope itemtype="http://schema.org/Person">';
              $organizers_output .= '<figcaption>';
                  $organizers_output .= '<span itemprop="name">'.get_field('first_name',$organizer->ID).' <span class="last-name">'.get_field('last_name',$organizer->ID).'</span></span>';
              $organizers_output .= '</figcaption>';
              $organizers_output .= '<img itemprop="image" src="'.$image.'" alt="">';
          $organizers_output .= '</figure>';
          
          $organizers_output .= '<ul class="overlay social-medias reversed">';
              if($fb_url = get_field('facebook_link',$organizer->ID))
                $organizers_output .= '<li><a href="'.$fb_url.'" class="Facebook"><img src="#" alt="Facebook"></a></li>';
              if($tw_url = get_field('twitter_link',$organizer->ID))
                $organizers_output .= '<li><a href="'.$tw_url.'" class="Twitter"><img src="#" alt="Twitter"></a></li>';
              if($ln_url = get_field('linkedin_link',$organizer->ID))
                $organizers_output .= '<li><a href="'.$ln_url.'" class="LinkedIn"><img src="#" alt="LinkedIn"></a></li>';
              if($gp_url = get_field('google_link',$organizer->ID))
                $organizers_output .= '<li><a href="'.$gp_url.'" class="Google+"><img src="#" alt="Google+"></a></li>';
          $organizers_output .= '</ul>';
          
      $organizers_output .= '</li>';
    }
    $organizers_output .= '</ul>';    
  }
      
  $organizers_output .= '</section>';
}


$volunteers_output = "";

$volunteers_args = array(
  	'posts_per_page'   => -1,
  	'orderby'          => 'menu_order',
  	'order'            => 'ASC',
  	'post_type'        => 'person',
  	'post_status'      => 'publish',
  	'tax_query'	=> array(
      array(
          'taxonomy'  => 'categorypersons',
          'field'     => 'slug',
          'terms'     => $volunteers_exclude, // exclude media posts in the news-cat custom taxonomy
          'operator'  => 'NOT IN'
          ),
     ),
  	'suppress_filters' => true );
  	
$volunteers = get_posts( $volunteers_args );

if(!empty($volunteers)){
  $volunteers_output .= '<ul class="volunteers-list">';
  foreach($volunteers as $volunteer){
    $volunteers_output .= '
        <li itemscope itemtype="http://schema.org/Person">
            <span itemprop="name">'.get_field('first_name',$volunteer->ID).' '.get_field('last_name',$volunteer->ID).'</span>
        </li>';
  }
  $volunteers_output .= '</ul>';    
}



?>
<!-- About section -->
<section id="<?php echo $post->post_name; ?>" class="about">
    <div class="l-section-wrapper">

        <header>
            <h2>À propos</h2>
        </header>

        <div class="l-row about-wrapper">

            <div class="l-col">
                <p class="strong"><?php echo get_field('org_title',$post->ID); ?></p>
                <p><?php echo get_field('org_text',$post->ID); ?></p>

                <?php echo $organizers_output; ?>
                
            </div>

            <div class="l-col">
                <div class="highlight">
                    <p class="strong"><?php echo get_field('vol_title',$post->ID); ?></p>
                    <p><?php echo get_field('vol_text',$post->ID); ?></p>
                </div>

                <?php echo $volunteers_output; ?>
                
            </div>

        </div>

    </div>
</section><!-- end of About section -->