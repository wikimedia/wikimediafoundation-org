<?php

// If Gutenberg RAMP plugin still exists, don't bypass the block editor.
if ( function_exists( 'gutenberg_ramp_load_gutenberg' ) ) {
	gutenberg_ramp_load_gutenberg();
}
