<?php
/**
 * Add new role: WMF Editor.
 */
add_action(
	'admin_init',
	function() {
		$ver = 1; // Incrementally update each time this code is changed.

		// Check if this has been run already.
		if ( $ver <= get_option( 'myplugin_roles_version' ) ) {
			return;
		}

		// Add a Reviewer role.
		wpcom_vip_add_role( 'wmf-editor', 'WMF Editor', get_role( 'author' )->capabilities );

		// Update the version to prevent this running again.
		update_option( 'myplugin_roles_version', $ver );
	}
);