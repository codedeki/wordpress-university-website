import $ from 'jquery';

class MyNotes {
    constructor() {
        this.events();
    }

    events() {
        $(".delete-note").on("click", this.deleteNote);
        $(".edit-note").on("click", this.editNote.bind(this)); //need .bind(this) to ensure 'this' equals the function editNote and doesn't equal whatever element is clicked on
        $(".update-note").on("click", this.updateNote.bind(this)); 
    }

    //Methods will go here
    editNote(e) {
        var thisNote = $(e.target).parents("li");
     if(thisNote.data("state") == "editable") {
            this.makeNoteReadOnly(thisNote);
       } else {
            this.makeNoteEditable(thisNote);
       }
    }

    makeNoteEditable(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i>Cancel');
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
        thisNote.find(".update-note").addClass("update-note--visible");
        thisNote.data("state", "editable");
    }

    makeNoteReadOnly(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i>Edit');
        thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field");
        thisNote.find(".update-note").removeClass("update-note--visible");
        thisNote.data("state", "cancel");
    }

    deleteNote(e) {
        var thisNote = $(e.target).parents("li"); //points to parent li
        //control type of request we send with .ajax()
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); //security: checks nonce number of user to permit or deny requests to delete notes
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'DELETE',
            success: (response) => {
                thisNote.slideUp(); //deletes note on click with slide up motion
                console.log("Congrats, request successful");
                console.log(response);
            },
            error: (response) => {
                console.log("Sorry, request failed");
                console.log(response);
            }
        });
    }

    updateNote(e) {
        var thisNote = $(e.target).parents("li"); //points to parent li
        //control type of request we send with .ajax()
        var updatedPost = {
            'title': thisNote.find(".note-title-field").val(),
            'content': thisNote.find(".note-body-field").val()
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); //security: checks nonce number of user to permit or deny requests to delete notes
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'POST',
            data: updatedPost,
            success: (response) => {
                this.makeNoteReadOnly(thisNote); //updates note on click
                console.log("Congrats, request successful");
                console.log(response);
            },
            error: (response) => {
                console.log("Sorry, request failed");
                console.log(response);
            }
        });
    }

}

export default MyNotes;