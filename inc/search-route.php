<?php 

add_action('rest_api_init', 'universityRegisterSearch');

//creates custom route in URL: wp-json/university/v1/search
function universityRegisterSearch() {
    //namespace version, route, array 
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE, //can also use 'GET' but not as safe
        'callback' => 'universitySearchResults'
    ));
}

//custom query: wordpress automatically converts PHP to JSON data in the custom URL
function universitySearchResults($data) {
   $mainQuery = new WP_Query(array(
       'post-type' => array('post', 'page', 'professor', 'program', 'campus', 'event'), //pull in multiple post types
       's' => sanitize_text_field($data['term']) //more security with sanitize //s for search; use custom param $data to make our search results dynamic, whatever the user types in is what is searched after wp-json/university/v1/search?term=
   ));

   //multiple arrays to separate content in search overlay
   $results = array(
       'generalInfo' => array(),
       'professors' => array(),
       'programs' => array(),
       'events' => array(),
       'campuses' => array()
   );

//loop through all of results
   while($mainQuery->have_posts()) {
    $mainQuery->the_post();

//funnel results in appropriate arrays
//array pushes whatever we specify into the JSON format so users don't have to download everything in university/v1/posts, but only what we want them to download within our custom university/v1/search 
    if(get_post_type() == 'post' OR get_post_type() == 'page') {
        array_push($results['generalInfo'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink()
        )); 
    }

    if(get_post_type() == 'professor') {
        array_push($results['professors'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink()
        )); 
    }

    if(get_post_type() == 'program') {
        array_push($results['programs'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink()
        )); 
    }

    if(get_post_type() == 'event') {
        array_push($results['events'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink()
        )); 
    }

    if(get_post_type() == 'campus') {
        array_push($results['campuses'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink()
        )); 
    }


   return $results; //get our custom data back in JSON format in wp-json/university/v1/search
}

}


?>