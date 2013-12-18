<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ): the_post(); 
	
	$thumb = get_post_thumbnail_id($post->ID);
	$img_url = wp_get_attachment_url( $thumb,'full' );
	
	if(function_exists('aq_resize')){
	  $image = aq_resize( $img_url, 710, 350, true );
	  if(empty($image)){
	    $image = aq_resize( $img_url, 710, 350, false );
	    if(empty($image)){
	      $image = $img_url;
	    }
	  }
	}
	else{
	  $image = $img_url;
	}
	
	$latest_blog_posts_output = "";
    $latest_blog_posts = get_posts('posts_per_page=3&exclude='.$post->ID);
	if(!empty($latest_blog_posts)){
	  $latest_blog_posts_output .= "<h2>Autres billets récents : </h2>";
	  $latest_blog_posts_output .= '<ul class="subevents">';
		foreach ($latest_blog_posts as $blog_post) {
		  $latest_blog_posts_output .= '<li itemprop="subEvent" itemscope itemtype="http://schema.org/Event">';
		  $latest_blog_posts_output .= '<h3 class="session-title" itemprop="name"><a href="'.get_permalink($blog_post->ID).'">'.$blog_post->post_title.'</a></h3>';
		  
		    $latest_blog_posts_output .= '<span class="session-speaker">
		        <span itemprop="name">'.get_the_author_meta('display_name',$blog_post->ID).'</span></span>';
		  
		  $latest_blog_posts_output .= '<span class="session-meta">';
		      $latest_blog_posts_output .= '<span class="session-date"><time datetime="'.trim(strftime("%Y-%m-%d",strtotime($blog_post->post_date))).'">'.strftime("%A %e %B",strtotime($blog_post->post_date)).'</time></span>'."\n";
		      $latest_blog_posts_output .= '</span>';
		  $latest_blog_posts_output .= '</span>';
		  
		  $latest_blog_posts_output .= '</li>';
		  unset($blog_post);
		}
	  $latest_blog_posts_output .= "</ul>";
	}
	
	?>
  <!-- Page content section -->
  <section id="content" class="page-content">
      <div class="l-section-wrapper">

          <div class="l-row">
              <div class="l-col">

                  <article>

                      <header class="content-header go-to-content">
                          <h1 itemprop="name"><?php the_title() ?></h1>
                          <div class="informations">
                              <span class="session-speaker">
                                  <span itemprop="name"><?php echo get_the_author(); ?></span>
                              </span>
                              <span class="session-meta">
                                  <span class="date"><time datetime="<?php echo trim(strftime("%Y-%m-%d",strtotime($post->post_date))); ?>"><?php echo strftime("%A %e %B %k h %M",strtotime($post->post_date)); ?></time></span>
                              </span>
                          </div>
                      </header>

                      <div class="content">
                          <?php if(!empty($image)){ echo '<p><img src="'.$image.'"></p>';}?>
                          <?php the_content(); ?>
                      </div>

                  </article>

                  <div  class="back-link"><a href="<?php echo get_bloginfo('url'); ?>">Retour à l'accueil</a></div>

              </div>
              <div class="l-col sidebar">

                <?php echo $latest_blog_posts_output; ?>

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
