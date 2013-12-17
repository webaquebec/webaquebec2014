<?php
/*
Template Name: Blogue
*/
global $post;

$blog_posts_args = array(
  	'posts_per_page'   => -1,
  	'orderby'          => 'post_date',
  	'order'            => 'DESC',
  	'post_status'      => 'publish',
  	'suppress_filters' => true );

$blog_posts = get_posts( $blog_posts_args );
$blog_output = "";
$blog_nav = "";

if($blog_posts){

  $blog_output .= '<div class="blog-wrapper"><div class="js-slider">
      <div class="blog-container js-slider-container" itemscope itemtype="http://schema.org/Blog">';

  foreach($blog_posts as $blog_post){
  
    $blog_output .= '<article class="slide" itemscope itemprop="blogPost" itemtype="http://schema.org/BlogPosting">';
        $blog_output .= '<header class="article-header">';
            $blog_output .= '<h3 class="article-title" itemprop="headline">';
                $blog_output .= '<a href="'.get_permalink($blog_post->ID).'">'.$blog_post->post_title.'</a>';
            $blog_output .= '</h3>';
            $blog_output .= '<p class="article-meta">';
                $blog_output .= '<time class="article-date" itemprop="datePublished" datetime="'.strftime("%Y-%m-%d",strtotime($blog_post->post_date)).'">'.strftime("%d/%m/%Y",strtotime($blog_post->post_date)).'</time>';
            $blog_output .= '</p>';
        $blog_output .= '</header>';
          
        $thumb = get_post_thumbnail_id($blog_post->ID);
        $img_url = wp_get_attachment_url( $thumb,'full' );
        
        if(function_exists('aq_resize')){
          $image = aq_resize( $img_url, '280', '130', true );
          if(!$image){
            $image = aq_resize( $img_url, '280', '130', false );
            if(!$image){
              $image = $img_url;
            }
          }
        }
        else{
          $image = $img_url;
        }
        
        if(!empty($image)){
          $blog_output .= '<div class="article-thumb">';
              $blog_output .= '<img src="'.$image.'" alt="">';
          $blog_output .= '</div>';
        }
        
        $blog_output .= '<div class="article-content" itemprop="description"><!-- extrait? -->';
            $blog_output .= get_excerpt_by_id($blog_post->ID);
        $blog_output .= '</div>';
        //$blog_output .= '<div class="article-readmore">';
            //$blog_output .= '<a href="'.get_permalink($blog_post->ID).'">Lire la suite <span class="visuallyhidden">de l\'article « '.$blog_post->post_title.' ».</span></a>';
        //$blog_output .= '</div>';
    $blog_output .= '</article>';
  }
  
  $blog_output .= '</div></div>';
  
  if(count($blog_posts) > 3){
    $blog_nav .= '<div class="blog-nav">';
      $blog_nav .= '<button class="button prev"><span class="visuallyhidden">Articles précédents</span></button>';
      $blog_nav .= '<button class="button next"><span class="visuallyhidden">Articles suivants</span></button>';
      
      for($i = 1; $i <= ceil(count($blog_posts)/3); $i++){
        $blog_nav .= '<button class="button-page"><span class="visuallyhidden">Page </span>'.$i.'</button>';
      }
    $blog_nav .= '</div>';
  }
  
  $blog_output .= $blog_nav . '</div>';

}
?>
<!-- Blog section -->
<section id="<?php echo $post->post_name; ?>" class="blog">
    <div class="l-section-wrapper">

        <header>
            <h2>Blogue</h2>
        </header>
        
        <?php echo $blog_output;?>

    </div>
</section><!-- end of Blog section -->