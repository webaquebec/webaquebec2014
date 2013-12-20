<?php
/*
Template Name: Pied de page
*/
global $post;

$waq_place_name = get_field('waq_place_name',$post->ID);
$waq_place_address = get_field('waq_place_address',$post->ID);
$waq_place_city = get_field('waq_place_city',$post->ID);
$waq_place_state = get_field('waq_place_state',$post->ID);
$waq_place_postal_code = get_field('waq_place_postal_code',$post->ID);

$waq_google_maps = $waq_place_name.' '.$waq_place_address.' '.$waq_place_city.', '.$waq_place_state;
$waq_google_maps = urlencode(str_replace(' ', '+', $waq_google_maps));

$questions_name = get_field('questions_name',$post->ID);
$questions_telephone = get_field('questions_telephone',$post->ID);
$questions_email = get_field('questions_email',$post->ID);

$medias_name = get_field('medias_name',$post->ID);
$medias_telephone = get_field('medias_telephone',$post->ID);
$medias_email = get_field('medias_email',$post->ID);

$hq_place_name = get_field('hq_place_name',$post->ID);
$hq_place_address = get_field('hq_place_address',$post->ID);
$hq_place_city = get_field('hq_place_city',$post->ID);
$hq_place_state = get_field('hq_place_state',$post->ID);
$hq_place_postal_code = get_field('hq_place_postal_code',$post->ID);
$hq_place_telephone = get_field('hq_place_telephone',$post->ID);

$output_social = "";
$social_hashtag = get_field('social_hashtag',$post->ID);
$social_facebook = get_field('social_facebook',$post->ID);
if(!empty($social_facebook)){
  $output_social .= '<a href="'.$social_facebook .'" class="Facebook"><img src="'.get_bloginfo('template_directory').'/img/icon-social-facebook.png" alt="Facebook"></a>';
}
$social_twitter = get_field('social_twitter',$post->ID);
if(!empty($social_twitter)){
  $output_social .= '<a href="'.$social_twitter .'" class="Twitter"><img src="'.get_bloginfo('template_directory').'/img/icon-social-twitter.png" alt="Twitter"></a>';
}
$social_linkedin = get_field('social_linkedin',$post->ID);
if(!empty($social_linkedin)){
  $output_social .= '<a href="'.$social_linkedin .'" class="LinkedIn"><img src="'.get_bloginfo('template_directory').'/img/icon-social-linkedin.png" alt="LinkedIn"></a>';
}
$social_google = get_field('social_google',$post->ID);
if(!empty($social_google)){
  $output_social .= '<a href="'.$social_google .'" class="Google+"><img src="'.get_bloginfo('template_directory').'/img/icon-social-googleplus.png" alt="Google+"></a>';
}

$archives = get_field('archives',$post->ID);
$archives_output = "";

foreach ($archives as $archive) {
  $archives_output .= '<li><a href="'.$archive['lien'].'"><abbr title="Web à Québec">WAQ</abbr> <span class="visuallyhidden">édition</span> '.$archive['year'].'</a></li>';
}

?>
<!-- Footer section -->
<footer class="footer">
    <div class="l-section-wrapper">

        <div class="visuallyhidden">
            <h2>Informations générales</h2>
        </div>

        <div class="l-row footer-infos">

            <div class="l-col">
                <div class="contact-element" itemscope itemtype="http://schema.org/Place">
                    <span class="name" itemprop="name"><?php echo $waq_place_name; ?></span>
                    <span class="adresse" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                        <span class="streetAddress" itemprop="streetAddress"><?php echo $waq_place_address; ?></span>
                        <span class="addressLocality" itemprop="addressLocality"><?php echo $waq_place_city; ?></span> (<?php echo $waq_place_state; ?>) <span class="postalCode" itemprop="postalCode"><?php echo $waq_place_postal_code; ?></span>
                    </span>
                    <span class="link"><a href="https://maps.google.ca/maps?q=<?php echo $waq_google_maps; ?>&amp;hl=fr&amp;ie=UTF8&amp;hq=<?php echo $waq_google_maps; ?>&amp;t=m&amp;z=16&amp;iwloc=A">Comment s'y rendre</a></span>
                </div>
            </div>

            <div class="l-col two-elements">
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

            <div class="l-col">
                <div class="contact-element" itemscope itemtype="http://schema.org/Place">
                    <span class="name" itemprop="name"><?php echo $hq_place_name;?></span>
                    <span class="adresse" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                        <span class="streetAddress" itemprop="streetAddress"><?php echo $hq_place_address;?></span>
                        <span class="addressLocality" itemprop="addressLocality"><?php echo $hq_place_city;?></span> (<?php echo $hq_place_state;?>) <span class="postalCode" itemprop="postalCode"><?php echo $hq_place_postal_code;?></span>
                        <span class="telephone" itemprop="telephone"><?php echo $hq_place_telephone;?></span>
                    </span>
                </div>
            </div>

            <div class="l-col large">
                <p class="strong">Suivez-nous sur le réseaux sociaux et collaborez avec nous</p>
                <p class="strong hashtag"><a href="https://twitter.com/search?q=<?php echo urlencode($social_hashtag); ?>&amp;src=typd"><?php echo $social_hashtag; ?></a></p>
                <div class="social-medias">
                    <?php echo $output_social; ?>
                </div>
            </div>

        </div>

        <div class="l-row footer-bar">

            <div class="l-col archives">
                <h3>Archives</h3>
                <ul>
                    <?php echo $archives_output; ?>
                </ul>
            </div>

            <div class="l-col right">
                <div><a href="<?php echo get_permalink(get_page_by_path('accessibilite')); ?>">Accessibilité</a></div>
                <div>
                    <span class="strong">Conception et réalisation :</span>
                    <a href="http://www.libeo.com" itemscope itemtype="http://schema.org/Organization">
                        <span itemprop="name">Libéo</span>
                    </a>
                </div>
            </div>

            <div class="l-col right">
                <div><a href="<?php echo get_permalink(get_page_by_path('accessibilite')); ?>">Accessibilité</a></div>
                <div>
                    <span class="strong">Conception et réalisation :</span>
                    <a href="http://www.libeo.com" itemscope itemtype="http://schema.org/Organization">
                        <span itemprop="name" class="visuallyhidden">Libéo</span>
                        <img src="<?php echo get_bloginfo('template_directory');?>/img/logo-libeo-footer.png" alt="">
                    </a>
                </div>
            </div>

        </div>

    </div>
</footer><!-- end of Footer section -->