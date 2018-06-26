/**
 *
 * Header JS
 *
 */


jQuery(document).ready(function($) {

  // Transation bar into slider
  $('.translation-bar ul').flickity({
    cellAlign: 'left',
    contain: true,
    pageDots: false,
    prevNextButtons: false,
    freeScroll: true
  });

  $('.arrow-wrap').on('click', function() {
    $('.translation-bar ul').flickity('next');
  });

  $('.back-arrow-wrap').on('click', function() {
    $('.translation-bar ul').flickity('prev');
  });

  function toggleNav() {
    $('.nav-links').toggleClass('open');
    $('.header-inner').toggleClass('nav-open');
  }

  function toggleSearch() {
    $('.search-toggle').blur();
    $('.search-bar-container input').focus();
  }

  function openSearch() {

    $('.search-overlay').fadeIn(500, function(){
      $('.nav-container, .search-overlay').addClass('search-open');
      $('.search-bar-container').addClass('is-open');
      toggleSearch();
    })
  }

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
    if ($(this).hasClass('search-open')) {
      closeSearch();
    }
  });

 });