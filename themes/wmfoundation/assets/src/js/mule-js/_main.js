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


  // Blockquotes
  // Since these blockquotes need to be display:inline for the quotation marks to function as designed, we can't add margin to it. This will wrap WordPress blockquotes in a container div to handle positioning and spacing.
  $('article blockquote').wrap( "<div class='blockquote-container'></div>")

  // Three up People module becomes slider on mobile.
  $('.people.slider-on-mobile').flickity({
    cellAlign: 'left',
    contain: true,
    pageDots: false,
    prevNextButtons: false,
    watchCSS: true
  });



  // Run a quick resize event on flickity - in case cards don't get spread properly spaces out cards.
  setTimeout(function(){
    $('.people.slider-on-mobile').flickity( 'resize' );
  }, 500);


  $('.news-categories .slider-on-mobile').flickity({
    cellAlign: 'left',
    contain: true,
    pageDots: false,
    prevNextButtons: false,
    watchCSS: true
  });


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
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 2000);
  });

});
