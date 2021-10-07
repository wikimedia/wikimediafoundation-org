/**
 *
 * Main site JS
 *
 */
jQuery(document).ready(function($) {

  // Related News module.  Keep h3 headings the same height so the images stay the same height as well.
  var headings = $('.related-news h3'),
    headingHeights = [];

  headings.each(function(index, value){
    headingHeights.push(value.clientHeight);
  });

  var maxHeight = headingHeights.reduce(function(a, b) {
    return Math.max(a, b);
  }, 0);

  headings.height(maxHeight);


  // Photo credits hover effect
  // Based on:
  // http://cssglobe.com/lab/tooltip/02/
  // http://cssglobe.com/lab/tooltip/02/main.js

  function imagePreview() {

    $( 'a.preview' ).hover( function() {
      this.t     = this.title;
      this.title = '';

      var c = ( this.t != '' ) ? this.t : '',
          b = document.createElement( 'br' ),
          p = document.createElement( 'p' ),
          i = document.createElement( 'img' );

      p.setAttribute( 'id', 'preview' );
      i.setAttribute( 'src', encodeURI( $(this).attr( 'data-src' ) ) );
      i.setAttribute( 'alt', 'Image Preview' );

      p.appendChild( i );

      if ( c !== '' ) {
		  p.appendChild( b );
		  p.appendChild( document.createTextNode( c ) );
      }

      $(this).append( p );

      $( '#preview' ).addClass( 'preview-visible' );
    },
    function() {
      this.title = this.t;
      $( '#preview' ).remove();
    } );
  };

  imagePreview();



  $('#dismiss-notification').click( function(){
    $('.notification-bar').fadeOut("slow", function(){
    });
  });

  $('a.niceScroll').on('click', function(event){
    event.preventDefault();
    var TOP_MARGIN = 100;
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top - TOP_MARGIN
    }, 1000);
  });

  // Move blocks in the ungrid
  if ( $('.ungrid').length > 0 ) {
    var maxHeight = 235;
    var h1Height = $('.ungrid h1').height();
    var diffHeight = maxHeight - h1Height;
    $('.ungrid-line').css( 'margin-top', ( -32 - diffHeight ) );

    var contentHeight = $('.ungrid .content').height() - 80;
    var ungridLineHeight = 224 + ( -32 - diffHeight );
    var amountToReduce = 50 - ungridLineHeight;

    if ( contentHeight > 200 ) {
        amountToReduce += contentHeight - 200;
    }

    $('.ungrid-line').css( 'height', ( 224 + amountToReduce ) );

    if ($(window).width() < 762) { // On mobile
      var contentBottom = $('.content').position().top + $('.content').outerHeight();
      var ungridTop = $('.ungrid-top-box').position().top;
      var eyebrow = ( $('.header-content .h4.eyebrow').length > 0 ) ? 25 : 0;
      $('#content').css('margin-top', contentBottom - ungridTop + 50 + eyebrow);
    }
  }

});
