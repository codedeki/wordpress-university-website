<?php 

require get_theme_file_path('/inc/search-route.php');

function university_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {return get_the_author();} 
    ));
}

add_action('rest_api_init', 'university_custom_rest');

function pageBanner($args = NULL) { //set equal to NULL to avoid errors in case we call the function in other pages without any paramaters
    
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }

    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!$args['photo']) {
        if(get_field('page_banner_background_image')) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }

    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>);"></div>  
        <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
        <div class="page-banner__intro">
            <p><?php echo $args['subtitle']; ?></p>
        </div>
        </div>  
    </div>
<?php }

function marsuniversity_files() {
    //wp_enqueue_script takes 3 arguments: dependancies? = Null; version? = '1.0' OR microtime(); load on bottom of page before closing body tag? = True.
    //microtime to prevent caching of files during development; remove at launch
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=ENTER API KEY', NULL, microtime(), true);
    wp_enqueue_script('university_main_js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, microtime(), true);  
    // load css or JS files NOT in index.html but in functions.php --gets css file from style.css
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime());
    //input js into html
    wp_localize_script('university_main_js', 'universityData', array(
        'root_url' => get_site_url()    //make dynamic relative link for deployement: invent key called root_url to store whatever site url the user enters; makes sure the user can dynamically load the JSON data from the HTTP request in Search.js getResults();
    )); 
}

add_action('wp_enqueue_scripts', 'marsuniversity_files'); //wordpress will decide when to call the function, during wp enqueue scripts event


function marsuniversity_features() {
    // register_nav_menu('headerMenuLocation', 'Header Menu Location'); //creates menu in admin appearance bar
    // register_nav_menu('footerMenuOne','Footer Menu One');  //creates custom menus
    // register_nav_menu('footerMenuTwo','Footer Menu Two'); 
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true); //custom name, width, neight, crop?
    add_image_size('professorPortrait', 480, 650, true); 
    add_image_size('pageBanner', 1500, 350, true);
}

//add matching title tag to each opened file
add_action('after_setup_theme', 'marsuniversity_features'); //run our function after setup theme event


function university_adjust_queries($query) { 
    
    //Campus event display
    if(!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()) {
        $query->set('posts_per_page', -1);
    }
    
    //Program event display
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


function universityMapKey($api) {
    $api['key'] = 'Enter API KEY';
    return $api;
}


add_filter('acf/fields/google_map/api', 'universityMapKey' ); //google maps api
?>