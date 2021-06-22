/**
 *
 * Header JS
 *
 */

jQuery(document).ready(function($) {
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
