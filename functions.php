<?php 

function marsuniversity_files() {
    //wp_enqueue_script takes 3 arguments: dependancies? = Null; version? = '1.0' OR microtime(); load on bottom of page before closing body tag? = True.
    //microtime to prevent caching of files during development; remove at launch
    wp_enqueue_script('university_main_js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, microtime(), true);
    // load css or JS files NOT in index.html but in functions.php --gets css file from style.css
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); //get working later
    wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime());
}

add_action('wp_enqueue_scripts', 'marsuniversity_files'); //wordpress will decide when to call the function, during wp enqueue scripts event

function marsuniversity_features() {
    // register_nav_menu('headerMenuLocation', 'Header Menu Location'); //creates menu in admin appearance bar
    // register_nav_menu('footerMenuOne','Footer Menu One');  //creates custom menus
    // register_nav_menu('footerMenuTwo','Footer Menu Two'); 
    add_theme_support('title-tag');
}

//add matching title tag to each opened file
add_action('after_setup_theme', 'marsuniversity_features'); //run our function after setup theme event

//Program event display
function university_adjust_queries($query) { 
    if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
//orders posts by event date and removes old dates in event archive; $query runs only on intended url; not globally or in the admin pages
    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date'); 
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC'); 
        $query->set('meta_query', array(
            array(
            'key' => 'event_date',
            'compare' => '>=', 
            'value' => $today,
            'type' => 'numeric'
            )
        )
    );

    }
}

add_action('pre_get_posts', 'university_adjust_queries');

?>