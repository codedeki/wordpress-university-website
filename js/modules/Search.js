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
      $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(), data => {
        this.resultsDiv.html(`
          <div class="row">
            <div class="one-third">
              
              <h2 class="search-overlay__section-title">General Information</h2>
              ${data.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
              ${data.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.postType == 'post' ? `by ${item.authorName}` : ''}</li>`).join('')}
              ${data.generalInfo.length ? '</ul>' : ''}
            </div>

            <div class="one-third">
              <h2 class="search-overlay__section-title">Programs</h2>
              ${data.programs.length ? '<ul class="link-list min-list">' : `<p>No programs match that search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}
              ${data.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
              ${data.programs.length ? '</ul>' : ''} 
              
              <h2 class="search-overlay__section-title">Professors</h2>
              ${data.professors.length ? '<ul class="professor-cards">' : `<p>No professors match that search.</p>`}
              ${data.professors.map(item => `
              <li class="professor-card__list-item">
              <a class="professor-card" href="${item.permalink}">
                <img class="professor-card__image" src="${item.image}" alt="professor_image">
                <span class="professor-card__name">${item.title}</span>
              </a>
            </li> 
              `).join('')}
              ${data.professors.length ? '</ul>' : ''} 
            </div>  

            <div class="one-third">
              <h2 class="search-overlay__section-title">Campuses</h2>
              ${data.campuses.length ? '<ul class="link-list min-list">' : `<p>No campuses match that search.<a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
              ${data.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
              ${data.campuses.length ? '</ul>' : ''} 
              
              <h2 class="search-overlay__section-title">Events</h2>
              ${data.events.length ? '' : `<p>No events match that search.<a href="${universityData.root_url}/events">View all events</a></p>`}
              ${data.events.map(item => `
              <div class="event-summary">
              <a class="event-summary__date t-center" href="${item.permalink}">
                  <span class="event-summary__month">${item.month}</span>
                  <span class="event-summary__day">${item.day}</span>  
              </a>
              <div class="event-summary__content">
                  <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
              <p> ${item.description} <a href="${item.permalink}" class="nu gray">Learn more</a></p> 
              </div>
          </div>
              `).join('')}
            </div>
          </div> 
      `);
      this.isSpinnerVisible = false; 
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
        return false; //prevents default behavior of a or link element when clicking on search button in top right corner (see header.php <a href="<?php esc_url(site_url('/search'))
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