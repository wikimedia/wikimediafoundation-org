/**
 *
 * Header JS
 *
 */


jQuery(document).ready(function($) {

  // Transation bar into slider
  $( '.translation-bar ul' ).flickity({
      rightToLeft: wmfrtl.enable,
      cellAlign: 'left',
      contain: true,
      pageDots: false,
      prevNextButtons: false,
      freeScroll: true
  } );

  $('.arrow-wrap').on('click', function() {
    $('.translation-bar ul').flickity('next');
  });

  $('.back-arrow-wrap').on('click', function() {
    $('.translation-bar ul').flickity('prev');
  });

  function toggleNav() {
    $('.nav-links').toggleClass('open');
    $('.header-inner').toggleClass('nav-open');

    $('.mobile-nav-toggle .icon-close').toggle();
    $('.mobile-nav-toggle .icon-menu').toggle();
    $('.mobile-cover').toggle().on('click', toggleNav);

    $('.logo-container_sm .icon-logo-horizontal').toggleClass('fade-20');
    $('.search-toggle').toggleClass('fade-20');

    if ($('.top-nav').hasClass('pinned')) {
      jQuery('.list-inline.open').css('top','50px');
    } else {
      jQuery('.list-inline.open').css('top','100px');
    }
  }

  function toggleSearch() {
    $('.search-toggle').blur();
    $('.search-bar-container input').focus();
  }

  function openSearch() {
    $('.search-overlay').fadeIn(500, function(){
      $('.search-form input').focus();
      $('.nav-container, .search-overlay').addClass('search-open');
      $('.search-bar-container').addClass('is-open');
      toggleSearch();
    })
  }

  $(document).keyup(function(e) {
     if (e.key === "Escape") {
        closeSearch();
    }
});

  function closeSearch() {
    $( '.search-bar-container' ).removeClass('is-open');
    $('.search-overlay').fadeOut(400, function(){
      $('.nav-container, .search-overlay').removeClass('search-open');
      toggleSearch();
    });
  }


  $('.mobile-nav-toggle').on('click', function() {
    toggleNav();
    // If search is open, close it.
    if ($('.search-open').length) {
      $('.logo-nav-container').removeClass('search-open');
    }
  });

  $('.search-toggle').on('click', function() {
    // If mobile nav is open, close it.
    if ($('.nav-open').length) {
      $('.nav-links').removeClass('open');
      $('.header-inner').removeClass('nav-open');
      openSearch();
    } else {
      openSearch();
    }
  });

  // On resize, remove any open nav classes if desktop size, otheriwise, blue overlay behind nav will still be there.
  $(window).on('resize', function() {

    if (window.matchMedia("(min-width: 1050px)").matches) {
      if ($('.nav-open').length) {
        $('.nav-links').removeClass('open');
        $('.header-inner').removeClass('nav-open');
      }
    }
  });

  // Hide search bar container if mouse is clicked outside of search div or search toggle and search div is open
  $('.search-overlay').on('click', function(e) {
    if ($(e.target).hasClass('search-open')) {
      closeSearch();
    }
  });

  $('.search-close-esc').on('click', closeSearch);
  $('.search-close-mobile').on('click', closeSearch);

  // Cycle the visions
  var visions = $('.vision');
  var currentVision = 0;

  function cycleVision() {
    var prevVision = currentVision;
    if ( currentVision === (visions.length-1) ) {
      currentVision = 0;
    } else {
      currentVision += 1;
    }
    $(visions[prevVision]).fadeOut(750, function () {
      $(visions[currentVision]).fadeIn(750);
    });

    window.setTimeout( cycleVision, 5000);
  }
  if (visions.length > 1) {
    window.setTimeout( cycleVision, 5000);
  }

 });
