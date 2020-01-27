import $ from 'jquery';

class Like {
    constructor() {
        this.events();
    }

    events() {
        $(".like-box").on("click", this.ourClickDispatcher.bind(this));

    }

    //methods
    //add or delete a like
    ourClickDispatcher(e) {
        var currentLikeBox = $(e.target).closest(".like-box"); //register if click anywhere on box

        if(currentLikeBox.attr('data-exists') == 'yes') { //data method only looks at data once, so for toggling b/w create/like we need attr method
            this.deleteLike(currentLikeBox);
        } else {
            this.createLike(currentLikeBox);
        }
    }

    //cf. like-route.php
    createLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); //nonce code; see like.js
            },
           url: universityData.root_url + '/wp-json/university/v1/manageLike', 
           type: 'POST',
           data: {'professorId': currentLikeBox.data('professor')}, //see like-route.php for backend and single-professor.php for 'professor' data
           success: (response) => {
               currentLikeBox.attr('data-exists', 'yes'); //fill in heart after click to like
               var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
               likeCount++; //increment like by one after click
               currentLikeBox.find(".like-count").html(likeCount); //output like count to html on screen
               currentLikeBox.attr("data-like", response); //send back id number of post so we can delete it on toggle later if desired
               console.log(response)
           },
           error: (response) => {
               console.log(response)
           }
        });
    }

    deleteLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); //nonce code; see like.js
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            data: {'like': currentLikeBox.attr('data-like', )},
            type: 'DELETE',
            success: (response) => {
               currentLikeBox.attr('data-exists', 'no'); //remove fill on heart after click to like
               var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
               likeCount--; //decrease like by one after click
               currentLikeBox.find(".like-count").html(likeCount); //output like count to html on screen
               currentLikeBox.attr("data-like", ''); //instead of sending back id number of post, we use empty string to get no response, which deletes the like from screen
               console.log(response)
            },
            error: (response) => {
                console.log(response)
            }
         });
    }
}

export default Like; 
