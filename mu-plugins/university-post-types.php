<?php function university_post_types() {
    register_post_type('event', array(
        'supports' => array('title', 'editor', 'excerpt'), //custom fields: ACF or CMB2 plugins
        'rewrite' => array('slug' => 'events'), //not working
        'has_archive' => true, //not working
        'public' => true,
        'labels' => array(
            'name' => 'Events',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items' => 'All Events',
            'singular_name' => 'Event' 
        ),
        'menu_icon' => 'dashicons-calendar-alt' 
    ));
} 

add_action('init', 'university_post_types');

?>