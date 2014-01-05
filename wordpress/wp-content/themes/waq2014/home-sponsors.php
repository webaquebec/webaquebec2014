<?php
/*
Template Name: Partenaires
*/
global $post;

$sponsors_levels_args = array(
  	'posts_per_page'   => -1,
  	'orderby'          => 'meta_value_num',
  	'order'            => 'ASC',
  	'meta_key'         => '_conferencer_order',
  	'post_type'        => 'sponsor_level',
  	'post_status'      => 'publish',
  	'suppress_filters' => true );

$sponsors_levels = get_posts( $sponsors_levels_args );

$sponsors_output = "";
foreach($sponsors_levels as $sponsors_level){
  
  $sponsors_args = array(
    	'posts_per_page'   => -1,
    	'orderby'          => 'meta_value_num',
    	'order'            => 'ASC',
    	'meta_key'         => '_conferencer_order',
    	'meta_query'       => array(
        	                    array(
        	                        'key' => '_conferencer_level',
        	                        'value' => $sponsors_level->ID,
        	                        'compare' => '='
        	                    )
          	                ),
    	'post_type'        => 'sponsor',
    	'post_status'      => 'publish',
    	'suppress_filters' => true );

  $sponsors = get_posts( $sponsors_args );
  
  if(!empty($sponsors)){
    $sponsors_output .= '<section class="sponsors-group">';
    $sponsors_output .= '<header><h3><span>'.$sponsors_level->post_title.'</span></h3></header>';
    $sponsors_output .= '<ul class="sponsors-list">';
    foreach($sponsors as $sponsor){
      $sponsors_output .= '<li itemscope itemtype="http://schema.org/Organization">';
          $sponsors_output .= '<a href="'.get_post_meta( $sponsor->ID, '_conferencer_url', true ).'">';
          
              
              $img_width = get_post_meta( $sponsors_level->ID, '_conferencer_logo_width', true );
              $img_height = get_post_meta( $sponsors_level->ID, '_conferencer_logo_height', true );
                
              $thumb = get_post_thumbnail_id($sponsor->ID);
              $img_url = wp_get_attachment_url( $thumb,'full' );
              
              if(function_exists('aq_resize') && $img_width && $img_height){
                $image = aq_resize( $img_url, $img_width, $img_height, false );
                if(!$image){
                  $image = $img_url;
                }
              }
              else{
                $image = $img_url;
              }
          
              $sponsors_output .= '<img itemprop="image" src="'.$image.'" alt="">';
              $sponsors_output .= '<span itemprop="name" class="visuallyhidden">'.$sponsor->post_title.'</span>';
          $sponsors_output .= '</a>';
      $sponsors_output .= '</li>';
    }
    $sponsors_output .= '</ul>'; 
    $sponsors_output .= '</section>';   
  }
}
?>
<!-- Partners section -->
<section id="<?php echo $post->post_name; ?>" class="sponsors">
    <div class="l-section-wrapper">

        <header>
            <h2>Partenaires</h2>
        </header>

        <?php echo $sponsors_output; ?>

    </div>
</section><!-- end of Partners section -->