<?php

$headerHomePage = get_posts(array('post_type' => 'page','meta_key' => '_wp_page_template','meta_value' => 'home-header.php'));
$headerHomePage= $headerHomePage[0];

?>

<!-- Home section -->
<header class="home">
    <div class="l-section-wrapper">

        <a href="#" class="visuallyhidden focusable l-ally">Passer au contenu</a>

        <h1 itemprop="name" class="visuallyhidden">Le Web à Québec</h1>

        <div class="event-logo">
            <a href="<?php echo get_bloginfo('url'); ?>">
                <img src="<?php bloginfo('template_directory'); ?>/img/logo.png" alt="">
            </a>
        </div>

        <div class="event-subscribe event-subscribe-menu"><a href="<?php echo get_field('eventbrite_link',$headerHomePage->ID); ?>">Achetez vos billets</a></div>
        <div class="event-back-link"><a href="<?php echo get_bloginfo('url'); ?>">Retour à <?php echo (is_singular('session')?"l'horaire":"l'accueil") ?></a></div>

    </div>
</header><!-- end of Home section -->