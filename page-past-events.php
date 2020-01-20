<?php 

get_header(); 

pageBanner(array(
  'title' => 'Past Events',
  'subtitle' => 'A recap of our past events.'
));

?>

  <div class="container container--narrow page-section">
  <?php 

    //create custom query for past-events
    $today = date('Ymd');
    $pastEvents = new WP_Query(array(
        'paged' => get_query_var('paged', 1), //ensures pagination works
        'posts_per_page' => 2,
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',  
        'order' => 'ASC',
        'meta_query' => array(
        array( //only show posts that are greater than or equal to today's date
            'key' => 'event_date',
            'compare' => '<', //event date is less than today = a past event
            'value' => $today,
            'type' => 'numeric' //we are comparing numbers so add this
        )
        )
    ));

    while($pastEvents->have_posts()) {
      $pastEvents->the_post(); ?>
          <div class="event-summary">
          <a class="event-summary__date t-center" href="#">
            <span class="event-summary__month"><?php 
              $eventDate = new DateTime(get_field('event_date'));
              echo $eventDate->format('M')  
            ?></span>
            <span class="event-summary__day"> <?php echo $eventDate->format('d'); ?></span>  
          </a>
          <div class="event-summary__content">
            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
            <p> <?php echo wp_trim_words(get_the_content(), 18); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p> 
          </div>
        </div>
    <?php } 
    //for custom queries, we need to pass paramaters as array to page_links
    echo paginate_links(array(
        'total' => $pastEvents->max_num_pages
    ));
  ?>

<hr class="section-break">

 <a href="<?php echo site_url('/events');?>"> Back to All Events</a></>
  </div> 


<?php get_footer();

?>

