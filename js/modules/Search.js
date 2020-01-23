import $ from 'jquery'; 

class Search {
    // 1. queries: describe & init objects
    constructor() {
        this.addSearchHTML(); //Js is synchronous, so must call this function first
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
                this.typingTimer = setTimeout(this.getResults.bind(this), 750);
            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
        }

        this.previousValue = this.searchField.val();
    }

    getResults() {
        //make dynamic relative link for deployement using our wp_localize_script function (universityData.root_url) in functions.php
        $.when(
            $.getJSON(universityData.root_url + '/wp-json/wp/v2/posts?search=' + this.searchField.val()),
            $.getJSON(universityData.root_url + '/wp-json/wp/v2/pages?search=' + this.searchField.val())
            ).then((posts, pages) => {
            var combinedResults = posts[0].concat(pages[0]);
            this.resultsDiv.html(`
              <h2 class="search-overlay__section-title">General Information</h2>
              ${combinedResults.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
                ${combinedResults.map(item => `<li><a href="${item.link}">${item.title.rendered}</a> ${item.type == 'post' ? `by ${item.authorName}` : ''}</li>`).join('')}
              ${combinedResults.length ? '</ul>' : ''}
            `);
            this.isSpinnerVisible = false;
          }, () => {
            this.resultsDiv.html('<p>Unexpected error; please try again.</p>');
          });
        }

    keyPressDispatcher(e) {
        //s key functionality + if another input on the page is focused then pressing s key won't open overlap
        if(e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')) {
            this.openOverlay();
        }
        //ESC key functionality
        if(e.keyCode == 27 && this.isOverlayOpen) {    
            this.closeOverlay(); 
        }
    }

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll"); //uses overflow:hidden, which removes ability to scroll
        this.searchField.val('');
        setTimeout(() => this.searchField.focus(), 301);
        this.isOverlayOpen = true;
    }
    
    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        setTimeout(() => this.searchField.blur(), 301);
        this.isOverlayOpen = false;
    }

    addSearchHTML() {
        $("body").append(`
        <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text"  class="search-term" placeholder="What are you looking for?" id="search-term"> 
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>
      
        <div class="container">
          <div id="search-overlay__results">
      
          </div>
        </div>
      </div>
        `);
    }

} 

export default Search;  