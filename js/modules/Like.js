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

        if(currentLikeBox.data('exists') == 'yes') {
            this.deleteLike(currentLikeBox);
        } else {
            this.createLike(currentLikeBox);
        }
    }

    //cf. like-route.php
    createLike(currentLikeBox) {
        $.ajax({
           url: universityData.root_url + '/wp-json/university/v1/manageLike', 
           type: 'POST',
           data: {'professorId': currentLikeBox.data('professor')}, //see like route.php for backend and single-professor.php for 'professor' data
           success: (response) => {
               console.log(response)
           },
           error: (response) => {
               console.log(response)
           }
        });
    }

    deleteLike() {
        $.ajax({
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            type: 'DELETE',
            success: (response) => {
                console.log(response)
            },
            error: (response) => {
                console.log(response)
            }
         });
    }
}

export default Like; 
