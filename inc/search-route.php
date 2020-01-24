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

//funnel results into appropriate arrays
//array pushes whatever we specify into the JSON format so users don't have to download everything in university/v1/posts, for eg., but only what we want them to download within our custom university/v1/search?term= 
    if(get_post_type() == 'post' OR get_post_type() == 'page') {
        array_push($results['generalInfo'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'postType' => get_post_type(),
            'authorName' => get_the_author()
        )); 
    }

    if(get_post_type() == 'professor') { 
        array_push($results['professors'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
        )); 
    }

    if(get_post_type() == 'program') {
        $relatedCampuses = get_field('related_campus'); //get advanced custom field name and loop through it

        if($relatedCampuses) {
            foreach($relatedCampuses as $campus) {
                array_push($results['campuses'], array(
                    'title' => get_the_title($campus),
                    'permalink' => get_the_permalink($campus)
                ));
            }
        }

        array_push($results['programs'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'id' => get_the_id()
        )); 
    }
    
    if(get_post_type() == 'campus') {
        array_push($results['campuses'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink()
        )); 
    }

    if(get_post_type() == 'event') {
        $eventDate = new DateTime(get_field('event_date'));
        $description = null;
        if (has_excerpt()) {  
            $description = get_the_excerpt();
        } else {
            $description = wp_trim_words(get_the_content(), 18); 
        }
        array_push($results['events'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'month' => $eventDate->format('M'),
            'day' => $eventDate->format('d'),
            'description' => $description
        )); 
    }
}

//new array for PROGRAMS section to display results associated with programs
if($results['programs']) { //executed only if there are relational programs; so we don't get results when typing random gibberish

    $programsMetaQuery = array('relation' => 'OR');
    
    foreach($results['programs'] as $item) {
        array_push($programsMetaQuery, array(
            'key' => 'related_programs',
            'compare' => 'LIKE',
            'value' => '"'. $item['id'] . '"'//get dynamic id of programs
            ));
    }
    
    $programRelationshipQuery = new WP_QUERY(array(
        'post-type' =>  array('professor', 'event'),
        'meta-query' => $programsMetaQuery
    ));
    
    while($programRelationshipQuery->have_posts()) {
        $programRelationshipQuery->the_post();
        
        if(get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()) {  
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18); 
            }
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            )); 
        }

        if(get_post_type() == 'professor') { 
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
            )); 
        }
    }
    
    //remove duplicate results from our array
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    $results['campuses'] = array_values(array_unique($results['campuses'], SORT_REGULAR));
}

return $results; //get our custom data back in JSON format in wp-json/university/v1/search?term=

}

?>