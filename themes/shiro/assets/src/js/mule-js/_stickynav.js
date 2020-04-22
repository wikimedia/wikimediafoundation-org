(function($, root, undefined) {
  $(function() {
    var $toc, $links, headings;

    $toc     = $('.toc');
    if ( $toc.length > 1 ) {
      $toc = $toc.last();
    }
    $links   = $toc.find('.toc-link');
    headings = [];

    // Stick the table of contents in its container
    $toc.Stickyfill();

    // Get a heading for each top-level link in the table of contents
    $links.each(function() {
      var $link, $heading;

      $link    = $(this);
      $heading = $($link.attr('href'));

      headings.push( new Heading($heading, $link) );
    });

    // On scroll, see which heading is closest and highlight it
    $(window).scroll(function() {
      debounce(checkHeadingPosition(headings), 500);
    });

    // And make sure that the position is checked on page load.
    checkHeadingPosition(headings)
  });

  // Loop through headings and see which one is in view
  function checkHeadingPosition(headings) {
    var scrollPosition = $(window).scrollTop();

    $.each(headings, function(i) {
      var heading = headings[i];

      if( scrollPosition > heading.getPosition() - 200 ) {
        resetHeadings(headings);
        heading.markActive();
      }
    })
  }

  // Return all headings to inactive state
  function resetHeadings(headings) {
    $.each(headings, function(i) {
      var heading = headings[i];

      heading.$link.removeClass('-active');
    });
  }

  // Heading object
  function Heading($el, $link) {
    this.$el = $el;
    this.$link = $link;
  }

  Heading.prototype.getPosition = function() {
    return this.$el.offset().top;
  }

  Heading.prototype.markActive = function() {
    this.$link.addClass('-active');
  }
})(jQuery, this);
