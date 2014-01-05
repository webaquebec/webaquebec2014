<?php
/*
Template Name: Lieu
*/
global $post;

$footerHomePage = get_posts(array('post_type' => 'page','meta_key' => '_wp_page_template','meta_value' => 'home-footer.php'));
$footerHomePage= $footerHomePage[0];

$questions_name = get_field('questions_name',$footerHomePage->ID);
$questions_telephone = get_field('questions_telephone',$footerHomePage->ID);
$questions_email = get_field('questions_email',$footerHomePage->ID);

$medias_name = get_field('medias_name',$footerHomePage->ID);
$medias_telephone = get_field('medias_telephone',$footerHomePage->ID);
$medias_email = get_field('medias_email',$footerHomePage->ID);



$waq_place_name = get_field('waq_place_name',$footerHomePage->ID);
$waq_place_address = get_field('waq_place_address',$footerHomePage->ID);
$waq_place_city = get_field('waq_place_city',$footerHomePage->ID);
$waq_place_state = get_field('waq_place_state',$footerHomePage->ID);

$waq_google_maps = $waq_place_name.' '.$waq_place_address.' '.$waq_place_city.', '.$waq_place_state;
$waq_google_maps = urlencode(str_replace(' ', '+', $waq_google_maps));
?>
<!-- Place section -->
<section id="<?php echo $post->post_name; ?>" class="place">
    <div class="l-section-wrapper">
        <div>
            <header>
                <h2><span>Lieu de</span> <span>l'événement</span></h2>
                <a href="https://maps.google.ca/maps?q=<?php echo $waq_google_maps; ?>&amp;hl=fr&amp;ie=UTF8&amp;hq=<?php echo $waq_google_maps; ?>&amp;t=m&amp;z=16&amp;iwloc=A" class="route">Obtenir l'itinéraire</a>
            </header>
    
            <div class="place-wrapper">
                <div class="contact">
                    <h2>Coordonnées</h2>
                    <div class="contact-element" itemscope itemtype="http://schema.org/Organization">
                        <span class="name" itemprop="name"><?php echo $questions_name;?></span>
                        <span class="telephone" itemprop="telephone"><?php echo $questions_telephone;?></span>
                        <span class="email" itemprop="email"><a href="mailto:<?php echo $questions_email;?>"><?php echo $questions_email;?></a></span>
                    </div>
                    <div class="contact-element" itemscope itemtype="http://schema.org/Organization">
                        <span class="name" itemprop="name"><?php echo $medias_name;?></span>
                        <span class="telephone" itemprop="telephone"><?php echo $medias_telephone;?></span>
                        <span class="email" itemprop="email"><a href="mailto:<?php echo $medias_email;?>"><?php echo $medias_email;?></a></span>
                    </div>
                </div>
                <div class="newsletter">
                    <h2>Abonnez-vous à l'infolettre du <abbr title="Web à Québec">WAQ</abbr></h2>
                    <?php echo do_shortcode('[mailchimpsf_form]'); ?>
                </div>
            </div>
        </div>
    </div>
    <p class="visuallyhidden">Le contenu Google Maps qui suit n'est pas accessible. <a href="https://maps.google.ca/maps?q=ESPACE+400E+BELL+100,+QUAI+SAINT-ANDR%C3%89+QU%C3%89BEC,+QC&amp;hl=fr&amp;ie=UTF8&amp;hq=ESPACE+400E+BELL+100,+QUAI+SAINT-ANDR%C3%89+QU%C3%89BEC,+QC&amp;t=m&amp;z=16&amp;iwloc=A">Accéder la carte Google Maps sur le site externe</a> </p>
    <div class="googlemap" tabindex="-1">
        <div id="gmap"></div>
    </div>
</section><!-- end of Place section -->