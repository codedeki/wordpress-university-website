<?php 

get_header();

while(have_posts()) {
    the_post(); 
    pageBanner();
    ?>

  <div class="container container--narrow page-section">

    <div class="generic-content">
    
    <div class="row group">
      <div class="one-third">
        <?php the_post_thumbnail('professorPortrait'); ?>
      </div>
      <div class="two-thirds">
        <?php 
          $likeCount = new WP_Query(array(
            'post_type' => 'like',
            'meta_query' => array(  //meta_query required to match ID number of professor with like
              array(
                'key' => 'liked_professor_id',
                'compare' => '=',
                'value' => get_the_ID()
              )
            )
          ));

          $existStatus = 'no';

          //prepares role for data-exists="yes" in html
          $existCount = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(  //meta_query required to match ID number of professor with like
              array(
                'key' => 'liked_professor_id',
                'compare' => '=',
                'value' => get_the_ID() //get professor id to count likes (counted in custom field plugin as number but stored as blog post in backend)
              )
            )
          ));

          if($existCount->found_posts) {
            $existStatus = 'yes';
          }


        ?>
      <!-- use data-exists="yes" in php to show filled in heart -->
        <span class="like-box" data-professor="<?php the_ID(); ?>" data-exists="<?php echo $existStatus; ?>">
          <i class="fa fa-heart-o" aria-hidden="true"></i>
          <i class="fa fa-heart" aria-hidden="true"></i> 
          <span class="like-count"><?php echo $likeCount->found_posts; ?></span>
        </span>
        <?php the_content(); ?>
      </div>
    </div>
  </div>
    
      <?php 
    
    $relatedPrograms = get_field('related_programs');

    if($relatedPrograms) {
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium">Subject(s) Taught</h2>';
      echo '<ul class="link-list min-list">';
      foreach($relatedPrograms as $program) { ?>
      <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>  
      <?php } 
      echo '</ul>';
    }
      ?>

    </div>


    <?php }

get_footer();

?>