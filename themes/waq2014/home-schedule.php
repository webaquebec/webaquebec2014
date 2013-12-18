<?php
/*
Template Name: Horaire
*/
global $post;

$schedule_categories_args = array(
  	'hide_empty' => 0 );
$schedule_categories = get_terms( 'theme' , $schedule_categories_args );

$schedule_categories_output = '<li><button data-slug="filter-all">Tous</button></li>';
foreach ($schedule_categories as $schedule_categories) {
  $schedule_categories_output .= '<li><button data-slug="filter-'.$schedule_categories->slug.'">'.$schedule_categories->name.'</button></li>';
}


?>
<!-- Schedule section -->
<section id="<?php echo $post->post_name; ?>" class="schedule">
    <div class="l-section-wrapper">

        <header>
            <h2>Horaire</h2>
            <div class="schedule-filters">
                <h3>Filtrer par thématique</h3>
                <ul>
                    <?php echo $schedule_categories_output; ?>
                </ul>
            </div>
        </header>

        <div class="facebook-connect">
            <button>Activez <em>Facebook Connect</em></button>
            <p>Activez la fonction <em>Facebook Connect</em> et  enregistrez votre horaire pour le WAQ.</br>
                Vous aurez accès à la fonction sur tous vos périphériques.</p>
        </div>
        <button class="facebook-logout">Facebook Logout</button>
      
        <?php 
        
          echo do_shortcode('[agenda]');
        
        ?>

    </div>
</section><!-- end of Schedule section -->
