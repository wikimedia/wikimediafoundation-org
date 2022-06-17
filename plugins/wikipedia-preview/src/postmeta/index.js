import { registerPlugin } from '@wordpress/plugins';

import WikipediaPreviewPostMetaDetectLinks from './wp-postmeta-detectlinks';

registerPlugin( 'wikipediapreview-postmeta-detectlinks', {
	render: WikipediaPreviewPostMetaDetectLinks,
	icon: null,
} );
