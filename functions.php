<?php 

require get_theme_file_path('/inc/like-route.php');
require get_theme_file_path('/inc/search-route.php');

function university_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {return get_the_author();} 
    ));
//register user note count to use in MyNotes.js
    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');} 
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
    wp_enqueue_style('university_main_styles', get_stylesheet_uri());
    //input js into html
    wp_localize_script('university_main_js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest') //randomly generate number for each user session when logged in
        //nonce = number only used once
        //make dynamic relative link for deployement: invent key called root_url to store whatever site url the user enters; makes sure the user can dynamically load the JSON data from the HTTP request in Search.js getResults();
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


//Redirect subscriber accounts out of admin and onto homepage (even if type /wp-admin in url)
add_action('admin_init', 'redirectSubsToFrontend');


function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit; // stop code once complete 
    }
}

//Hide admin bar for subscriber accounts
add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
        // stop code once complete 
    }
}

//Customize Login Screen (remove wp logo)
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
    return esc_url(site_url('/')); //redirect user to our home page instead of wordpress when clicking logo
}


//Customize Login Screen CSS
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
    wp_enqueue_style('university_main_styles', get_stylesheet_uri()); //our custom css to override wp default css
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
}

add_filter('login_headertitle', 'ourLoginTitle');

//Customize Login Screen Text
function ourLoginTitle() {
    return get_bloginfo('name'); //get our site name
}

//SECURITY UPDATE
add_filter('wp_insert_post_data', 'makeNoteSecure', 10, 2); //10 default priority(lower the # the earlier function runs), 2 paramaters

function makeNoteSecure($data, $postarr) {
    //Force Note Posts that exist and aren't deleted by user to Be Private in Back End with wp_insert_post_data function
    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {  
        $data['post_status'] = "private"; 
    }
    //prevent adding empty notes
    if($data['post_type'] == 'note') {
        if($data['post_title'] == "" or $data['post_content'] == "") { 
            die("Both the title and content are required fields.");
        } 
    //limit user to make no more than 4 note posts and ensure we can still delete/edit posts after reaching limit (only new posts have an ID #, so ID is passed into second param as second condition)
    if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
        die("You have reached your note limit."); //die prevents below code from running if reach limit of 4
    }
    $data['post_content'] = sanitize_textarea_field($data['post_content']); //sanitize whatever user submits to body of note (remove JS and HTML tags for security reasons)
    $data['post_title'] = sanitize_text_field($data['post_title']); //sanitize title of note
    }

    return $data; //all data about post saved into database; if die, this will not be returned and an error results
} 

?>