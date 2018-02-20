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
    $('.logo-nav-container').toggleClass('search-open');
    $('.search-toggle').blur();
    $('.search-bar-container input').focus();
  }


  $('.mobile-nav-toggle').on('click', function() {
    toggleNav();

    // If search is open, close it.
    if ($('.search-open').length) {
      $('.logo-nav-container').removeClass('search-open');
    }
  });


  $('.search-toggle').on('click', function() {
    toggleSearch();

    // If mobile nav is open, close it.
    if ($('.nav-open').length) {
      $('.nav-links').removeClass('open');
      $('.header-inner').removeClass('nav-open');
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
  $('html').on('click', function(e) {
    if( !$(e.target).is('.search-toggle *, .search-toggle') ) {
      if( !$(e.target).is('.search-bar-container *, .search-bar-container') ) {
        if ($('.search-open').length) {
          $('.logo-nav-container').removeClass('search-open');
        }
      }
    }
  });


  // Move search bar container just below logo-nav-container for mobile
  function searchBar() {
    if (window.matchMedia("(max-width: 1049px)").matches) {
      $('.logo-nav-container').prepend($('.search-bar-container'));
    } else {
      $('.search-bar-container').insertAfter($('.search-toggle'));
    }
  }


  // Logo swapping depending on screen size.
  var logoStacked = $('.header-inner .logo-stacked'),
    logoFull = $('.header-inner .logo-full');

  function swapLogos() {
    if (window.matchMedia("(max-width: 1049px)").matches) {
        $('.header-inner .logo-container a').prepend(logoFull);
    } else {
      $('.header-inner .logo-container a').prepend(logoStacked);
    }
  }

  $(window).on('resize load', function() {
    swapLogos();
    searchBar();
  });
 });