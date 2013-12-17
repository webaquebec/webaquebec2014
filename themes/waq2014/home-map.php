<?php
/*
Template Name: Lieu
*/
global $post;

$questions_name = get_field('questions_name',$post->ID);
$questions_telephone = get_field('questions_telephone',$post->ID);
$questions_email = get_field('questions_email',$post->ID);

$medias_name = get_field('medias_name',$post->ID);
$medias_telephone = get_field('medias_telephone',$post->ID);
$medias_email = get_field('medias_email',$post->ID);
?>
<!-- Place section -->
<section id="<?php echo $post->post_name; ?>" class="place">
    <div class="l-section-wrapper">
        <div>
            <header>
                <h2><span>Lieu de</span> <span>l'événement</span></h2>
                <a href="#" class="route">Obtenir l'itinéraire</a>
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
                    <form method="post" action="#">
                        <div class="errors"></div>
                        <div class="field email">
                            <label for="email"><span class="visuallyhidden">Courriel</span></label>
                            <input type="text" id="email" class="input-email" value="" placeholder="Courriel">
                        </div>
                        <div class="buttons">
                            <input type="submit" value="Envoyer" class="input-submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="googlemap">
        <div id="gmap"></div>
    </div>
</section><!-- end of Place section -->