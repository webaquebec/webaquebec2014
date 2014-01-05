<?php 

  get_header();
  
  $page_args = array(
    	'posts_per_page'   => -1,
      'orderby'          => 'menu_order',
      'order'            => 'ASC',
      'meta_query'       => array(
        	                    array(
        	                        'key' => 'in_home_page',
        	                        'value' => 1,
        	                        'compare' => '='
        	                    )
          	                ),
      
      'post_type' => 'page',
  );
  
  $pages = get_posts($page_args); 
  global $post;
  
  foreach($pages as $post){
      setup_postdata( $post );
      $template_name = str_replace('.php', '', get_post_meta( $post->ID, '_wp_page_template', true ));
      $template_name = explode('-',$template_name);
      get_template_part($template_name[0],$template_name[1]);
  }
  
  wp_reset_postdata();
  get_footer();