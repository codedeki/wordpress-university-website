<?php 

get_header(); 

while(have_posts()) {
    the_post(); 
    
    pageBanner(array(
      'photo' => 'https://images.unsplash.com/photo-1577464849471-8dc8be232176?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80'
    )); 
    ?>
 
 

  <div class="container container--narrow page-section">

    <?php 
        $theParentId = wp_get_post_parent_id(get_the_ID());
        if($theParentId) { ?>    
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($theParentId); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParentId); ?></a> <span class="metabox__main"><?php the_title();?></span></p> 
    </div>
     <?php   }
    ?>

    <?php 
    $testArray = get_pages(array(
      'child_of' => get_the_ID() 
    ));
    
    // <!-- adds permalinks for pages that do have a parent or child -->
    // if false returns nothing, removing the permalinks from pages without parent or child
    if ($theParentId or $testArray) { ?>
    <div class="page-links">
      <h2 class="page-links__title"><a href="<?php echo get_permalink($theParentId);?>"><?php echo get_the_title($theParentId); ?></a></h2>
      <ul class="min-list">
          <?php 
            if($theParentId) {
              $findChildrenOf = $theParentId;  //if on parent post, get the ID of parent; else get ID of the current page which happens to be the child page
            } else {
              $findChildrenOf = get_the_ID(); //store in variable so the function doesn't echo all ID's of every page we have in WordPress

            }

            wp_list_pages(array(
              'title_li' => NULL,
              'child_of' => $findChildrenOf,
              'sort_column' => 'menu_order'
            )); //takes associative array
          ?>
      </ul>
    </div>
    <?php } ?>
  

    <div class="generic-content">
      <?php the_content(); ?>
    </div>

  </div>

<?php }

get_footer();

?>