<?php function university_post_types() {

//Campus Post Type
register_post_type('campus', array(
   'capability_type' => 'campus',  //NEED FOR NEW CUSTOM USER permissions with Members PlugIn
   'map_meta_cap' => true,  //NEED FOR NEW CUSTOM USER permissions with Members PlugIn
   'supports' => array('title', 'editor', 'excerpt'), //custom fields: ACF or CMB2 plugins
   'rewrite' => array('slug' => 'campuses'), 
   'has_archive' => true, 
   'public' => true,
   'labels' => array(
       'name' => 'Campuses',
       'add_new_item' => 'Add New Campus',
       'edit_item' => 'Edit Campus',
       'all_items' => 'All Campuses',
       'singular_name' => 'Campus' 
   ),
   'menu_icon' => 'dashicons-location-alt' 
));

//Event Post Type
register_post_type('event', array(
   'capability_type' => 'event', //makes event a unique event type to edit rather than default as post type
   'map_meta_cap' => true, //map and require the right capabilities: ensures appropriate permissions are needed for users who edit the event type
   'supports' => array('title', 'editor', 'excerpt'), //custom fields: ACF or CMB2 plugins
   'rewrite' => array('slug' => 'events'), 
   'has_archive' => true, 
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

//Program Post Type
register_post_type('program', array(
   'supports' => array('title'), //custom fields: ACF or CMB2 plugins
   'rewrite' => array('slug' => 'programs'),
   'has_archive' => true, 
   'public' => true,
   'labels' => array(
       'name' => 'Programs',
       'add_new_item' => 'Add New Program',
       'edit_item' => 'Edit Program',
       'all_items' => 'All Programs', 
       'singular_name' => 'Program' 
   ),
   'menu_icon' => 'dashicons-awards' 
));

//Professor Post Type
register_post_type('professor', array(
   'show_in_rest' => true,
   'supports' => array('title', 'editor', 'thumbnail'), //custom fields: ACF or CMB2 plugins
   'public' => true,
   'labels' => array(
       'name' => 'Professors',
       'add_new_item' => 'Add New Professor',
       'edit_item' => 'Edit Professors',
       'all_items' => 'All Professors',
       'singular_name' => 'Professor' 
   ),
   'menu_icon' => 'dashicons-welcome-learn-more' 
));

//Note Post Type
register_post_type('note', array(
   'show_in_rest' => true,
   'supports' => array('title', 'editor'), //custom fields: ACF or CMB2 plugins
   'public' => false,
   'show_ui' => true, //show in admin dashboard desipte false for public searches
   'labels' => array(
       'name' => 'Notes',
       'add_new_item' => 'Add New Note',
       'edit_item' => 'Edit Notes',
       'all_items' => 'All Notes',
       'singular_name' => 'Note' 
   ),
   'menu_icon' => 'dashicons-welcome-write-blog'  
));

} 

add_action('init', 'university_post_types');

?>