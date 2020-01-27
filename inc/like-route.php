<?php 


add_action('rest_api_init', 'universityLikeRoutes');
// Two routes for post and delete requests
function universityLikeRoutes() {
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data) {
    $professor = sanitize_text_field($data['professorId']); //see Like.js for professorId data
    wp_insert_post(array(
        'post_type' => 'like',
        'post_status' => 'publish', //defaults to draft
        'post_title' => '2nd PHP test',
        'meta_input' => array(
            'liked_professor_id' => $professor, //our custom field key in plugin


        )
    ));
}

function deleteLike() {
    return 'Thanks for trying to delete a like.';
}

?>