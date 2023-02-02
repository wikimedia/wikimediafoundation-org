<?php
/**
 * Add new role: WMF Editor.
 */
add_role( 'wmf-editor', 'WMF Editor', get_role( 'author' )->capabilities );