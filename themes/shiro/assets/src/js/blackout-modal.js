(function () {
	'use strict';

	var page = document.getElementById( 'page' ),
		modalClassName = 'blackout-modal',
		modal = document.querySelector( '.blackout-modal' ),
		closeButton = document.querySelector( '.close-blackout-modal' ),
		cookieName = modal ? modal.dataset.cookie : null;

	/**
	 * Set the cookie using the expiration passed in.
	 *
	 * @param {number} expiration Expiration in milliseconds.
	 * @return {void}
	*/
	function setCookie( expiration ) {
		var expires,
			date = new Date();

		// eslint-disable-next-line  no-magic-numbers
		date.setTime( date.getTime() + ( expiration || 30 * 24 * 60 * 60 * 1000 ) ); // Defaults to 30 days expiration. 30 days, 24 hours, 60 minutes, 60 seconds, 1000 for milliseconds.

		expires = ';expires=' + date.toUTCString();

		// Set cookie.
		document.cookie = cookieName + '=true;path=/' + expires;
	}

	/**
	 * Close the modal.
	 *
	 * @returns {void}
	*/
	function closeModal() {
		// Reset classname.
		modal.className = modalClassName;

		// Set aria-hidden attribute as false.
		page.setAttribute('aria-hidden', false);
	}

	/**
	 * Displays the modal by adding the `opened` className.
	 *
	 * @returns {void}
	 */
	function displayModal() {
		modal.className = modalClassName + ' opened';
		// Set the focus on the close button.
		closeButton.focus();

		// Set aria-hidden attribute as true.
		page.setAttribute('aria-hidden', true);
	}

	if ( modal ) {
		// If the cookie is not set and the element is present, add event listener to display the modal.
		if ( ! document.cookie.match( '(^|[^;]+)\\s*' + cookieName + '\\s*=\\s*([^;]+)' ) ) {
			displayModal();
		}

		closeButton.addEventListener( 'click', function () {
			setCookie();
			closeModal();
		} );
	}
} )();
