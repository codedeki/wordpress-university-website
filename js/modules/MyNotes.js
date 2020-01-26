import $ from 'jquery';

class MyNotes {
    constructor() {
        this.events();
    }

    events() {
        $("#my-notes").on("click", ".delete-note", this.deleteNote);
        $("#my-notes").on("click", ".edit-note", this.editNote.bind(this)); //need .bind(this) to ensure 'this' equals the function editNote and doesn't equal whatever element is clicked on
        $("#my-notes").on("click", ".update-note", this.updateNote.bind(this)); 
        $(".submit-note").on("click", this.createNote.bind(this)); 
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
                 //remove error message in UI if over note limit
                 if(response.userNoteCount < 5) {
                    $(".note-limit-message").removeClass("active"); 
                }
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
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); //security: checks user's nonce number to permit or deny requests to delete notes
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

    createNote() {
        var newPost = {
            'title': $(".new-note-title").val(),
            'content': $(".new-note-body").val(),
            'status': 'publish' // security: set to Private in the BACK END for better security (see makeNotePrivate() in functions.php)
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce); //security: checks user's nonce number to permit or deny requests to create notes
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/',
            type: 'POST',
            data: newPost,
            success: (response) => {
                $(".new-note-title, .new-note-body").val(''); //clear text input to blank after creating new note
                $(`
                <li data-id="${response.id}"> 
                <input readonly class="note-title-field" value="${response.title.raw}">
                    <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
                    <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>
                <textarea readonly class="note-body-field">${response.content.raw}</textarea>
                <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
                </li>
                `).prependTo("#my-notes").hide().slideDown();
                console.log("Congrats, request successful");
                console.log(response);
            },
            error: (response) => { 
                //show error message in UI if over note limit 
                if(response.responseText == "You have reached your note limit.") {
                    $(".note-limit-message").addClass("active"); 
                }
                console.log("Sorry, request failed"); 
                console.log(response);
            }
        });
    }

}

export default MyNotes;