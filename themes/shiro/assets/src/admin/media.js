/**
 * Extends wp.media.view.AttachmentCompat
 * Override initialize to stop listening the compat change to
 * prevent re-rendering the attachment compat view.
 * Fixes issue losing focus on atatchment custom fields in the modal.
 *
 * From ticket: https://core.trac.wordpress.org/ticket/40909
 */

if ( wp.hasOwnProperty( 'media' ) ) {
	var OriginalAttachmentCompat = wp.media.view.AttachmentCompat;
	wp.media.view.AttachmentCompat = OriginalAttachmentCompat.extend( {

		initialize: function() {
			OriginalAttachmentCompat.prototype.initialize.apply( this, arguments );

			if ( true === this.model.hasChanged() ) {
				this.stopListening( this.model, 'change:compat', this.render );
			}
		}
	} );
}
