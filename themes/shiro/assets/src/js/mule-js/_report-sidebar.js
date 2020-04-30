( function() {
	'use strict';

	var reportNav = document.querySelector( '.report-nav' ),
		mobileToggle;

	// Visually match height of mobile toggle to the visible or top <li> item.
	function updateToggleHeight() {
		var isOpen = reportNav.classList.contains( 'menu--expanded' ),
			heightItemSelector = isOpen ? '.toc-link-item > a' : '.toc-link-item.active > a',
			heightItem = reportNav.querySelector( heightItemSelector ),
			clientRect = heightItem && heightItem.getBoundingClientRect();

		if ( ! heightItem || ! mobileToggle || ! clientRect || ! clientRect.height ) {
			return;
		}

		mobileToggle.setAttribute( 'style', 'height:' + Math.floor( clientRect.height ) + 'px' );
	}

	if ( ! reportNav ) {
		return;
	}

	mobileToggle = reportNav.querySelector( '[data-menu-toggle]' );
	mobileToggle.addEventListener( 'click', function() {
		reportNav.classList.toggle( 'menu--expanded' );
		updateToggleHeight();
	} );

	// Ensure button starts off the right height.
	updateToggleHeight();
} )();
