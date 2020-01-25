<?php 

//ensures /my-notes is only available to logged in user
if(!is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/'))); //redirect user not logged in back to home page if try to get /my-notes url
    exit;
}

get_header(); 

while(have_posts()) {
    the_post(); 
    
    pageBanner(array(
      'photo' => 'https://images.unsplash.com/photo-1577464849471-8dc8be232176?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80'
    )); 
    ?>
 
  <div class="container container--narrow page-section">
     <ul class="min-list link-list" id="my-notes">
        <?php 
            $userNotes = new WP_QUERY(array(
                'post_type' => 'note',
                'posts_per_page' => -1,
                'author_id' => get_current_user_id()
            ));

            while($userNotes->have_posts()) {
                $userNotes->the_post(); ?>
                <li data-id="<?php the_ID(); ?>"> 
                    <input readonly class="note-title-field" value="<?php echo esc_attr(get_the_title()); ?>">
                        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
                        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>
                    <textarea readonly class="note-body-field" cols="30" rows="10"><?php echo esc_attr(wp_strip_all_tags(get_the_content())); ?></textarea>
                    <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
                </li>
            <?php }
        ?>
     </ul>
  </div>

<?php }

get_footer();

?>