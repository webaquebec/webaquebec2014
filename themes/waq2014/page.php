<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ): the_post(); 	?>
  <!-- Page content section -->
  <section id="content" class="page-content">
      <div class="l-section-wrapper">

          <div class="l-row">
              <div class="l-col">

                  <article>

                      <header class="content-header">
                          <h1 itemprop="name"><?php the_title() ?></h1>
                      </header>

                      <div class="content">
                          <?php the_content(); ?>
                      </div>

                  </article>

                  <div  class="back-link"><a href="<?php echo get_bloginfo('url'); ?>">Retour à l'accueil</a></div>

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
