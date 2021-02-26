<?php

namespace WMF\Editor;

function filter_blocks( $allowed_blocks ) {
	return [
		'core/paragraph',
		'core/image',
		'core/heading',
		'core/list',
		'core/table',
		'core/audio',
		'core/video',
		'core/file',
		'core/columns',
		'core/column',
		'core/group',
		'core/separator',
		'core/spacer',
		'core/embed',
		'core/freeform',
		'core/missing',
		'core/block',
		'core/button',
		'core/buttons',
		'core/latest-posts',
	];
}

function bootstrap() {
	add_action( 'allowed_block_types', __NAMESPACE__ . '\\filter_blocks' );
}
