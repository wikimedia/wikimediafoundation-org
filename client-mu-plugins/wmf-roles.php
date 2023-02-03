<?php
/**
 * Add new role: WMF Editor.
 */
wpcom_vip_add_role( 'wmf-editor', 'WMF Editor', get_role( 'author' )->capabilities );