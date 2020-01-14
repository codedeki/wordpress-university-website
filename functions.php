<?php 

function marsuniversity_files() {
    //2 more arguments: dependancies? = Null, version = '1.0', load on bottom of page before closing body tag?
    //microtime to prevent caching of files during development; remove at launch
    wp_enqueue_script('university_main_js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, microtime(), true);
    // load css or JS files NOT in index.html but in functions.php --gets css file from style.css
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); //get working later
    wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime());
}

add_action('wp_enqueue_scripts', 'marsuniversity_files'); //wordpress will decide when to call the functioin

?>