jQuery(document).ready(function($) {

  // Hover on an element, and add class to closest parent.
  function hoverAddClass(container, parent, elem, className) {
    $(container + ' ' + elem).hover(
      function () {
        $(this).closest(parent).addClass(className);
      },
      function () {
        $(this).closest(parent).removeClass(className);
      }
    );
  }

  // News category list, give li item a bottom border turquoise.
  hoverAddClass('.news-categories', 'li', 'a', 'border-turquoise');

  // Related News, take down the opacity of the darkening gradient.
  hoverAddClass('.related-news', '.bg-img', '.card-heading', 'headline-hover');

  // Translation bar, add underline to li
  hoverAddClass('.translation-bar', 'li', 'a', 'hover-underline');

  // Darken news images when headline is hovered
  hoverAddClass('.cta-news', '.card', '.h2 a', 'darken-img');
  hoverAddClass('.card-list-container', '.card', '.h3 a', 'darken-img');

});

svg4everybody();
