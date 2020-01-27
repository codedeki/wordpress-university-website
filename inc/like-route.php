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
    if(is_user_logged_in()) { //need nonce code to evaluate to true; see Like.js
        $professor = sanitize_text_field($data['professorId']); //see Like.js for professorId data

            $existCount = new WP_Query(array(
                'author' => get_current_user_id(), //will evaluate to 0 unless we use is_logged_in
                'post_type' => 'like',
                'meta_query' => array(  //meta_query required to match ID number of professor with like
                array(
                    'key' => 'liked_professor_id',
                    'compare' => '=',
                    'value' => $professor
                )
                )
            ));
        //User can only like each professor once 
    if($existCount->found_posts == 0 AND get_post_type($professor) == 'professor') {
        //create new like post
        return wp_insert_post(array( 
            'post_type' => 'like',
            'post_status' => 'publish', //defaults to draft
            'post_title' => '2nd PHP test',
            'meta_input' => array(
                'liked_professor_id' => $professor, //our custom field key in plugin
            )
        ));
    } else {
        die("You already liked this professor");
    }
    } else {
        die("Only logged in users can create a like.");
    }
}

function deleteLike($data) {
    $likeId = sanitize_text_field($data['like']);
    //can only delete like if you are the author of like
    if(get_current_user_id() == get_post_field('post_author', $likeId) AND 
        get_post_type($likeId) == 'like') {
            wp_delete_post($likeId, true); // true: remove permenantly from trash upon delete
            return 'Like deleted.';
        } else {
            die("You do not have permission to delete that."); 
        }

}

?>