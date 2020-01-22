import $ from 'jquery'; 

class Search {
    // 1. queries: describe & init objects
    constructor() {
        this.resultsDiv = $("#search-overlay__results");
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay"); 
        this.searchField = $("#search-term");
        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;
        this.typingTimer; //call to reset timer
        this.previousValue;
        this.events();
    };

    // 2. events
    events() {
        //must use .bind on the this keyword since .on changes the this keyword to point to the event that triggered the on (i.e. the html element that is clicked 'on')
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this)); 
        $(document).on("keydown", this.keyPressDispatcher.bind(this)); 
        this.searchField.on("keyup", this.typingLogic.bind(this));
    }


    // 3. methods (functions, action...)

    typingLogic() {
        
        if(this.searchField.val() != this.previousValue) {
            clearTimeout(this.typingTimer);
            //outputs spinner animation
            if(this.searchField.val()) {
                if(this.isSpinnerVisible == false) {
                    this.resultsDiv.html('<div class="spinner-loader"></div>');
                    this.isSpinnerVisible = true;
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
        }

        this.previousValue = this.searchField.val();
    }

    getResults() {
        this.resultsDiv.html("Imagine real search results here");
        this.isSpinnerVisible = false;
    }

    keyPressDispatcher(e) {
        //s key functionality + if another input on the page is focused then pressing s key won't open overlap
        if(e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')) {
            this.openOverlay();
        }
        //ESC key functionality
        if(e.keyCode == 27 && !this.isOverlayOpen) {
            this.closeOverlay();
        }
    }

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll"); //uses overflow:hidden, which removes ability to scroll
        this.isOverlayOpen = true;
    }
    
    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false;
    }

} 

export default Search;  