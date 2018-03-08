( function( $ ) {
	wp.customizerCtrlEditor = {

		init: function() {

			$( window ).load( function() {

				$( 'textarea.wp-editor-area' ).each( function() {
					var textArea = $(this),
						id = textArea.attr('id'),
						editor = tinyMCE.get(id),
						setChange,
						content;

					if ( editor ) {
						editor.onChange.add( function ( ed ) {
							ed.save();
							content = editor.getContent();
							clearTimeout( setChange );
							setChange = setTimeout( function() {
								textArea.val( content ).trigger( 'change' );
							}, 500 );
						});
					}

					textArea.css( {
						visibility: 'visible'
					} ).on( 'keyup', function() {
						content = textArea.val();
						clearTimeout( setChange );
						setChange = setTimeout( function(){
							content.trigger( 'change' );
						}, 500 );
					} );
				} );
			} );
		}

	};

	wp.customizerCtrlEditor.init();

} )( jQuery );