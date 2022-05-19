<?php
/**
 * Additional functionality for the shiro/linked-toc block.
 *
 * This block is defined in the block JS.
 */

namespace WMF\Editor\Blocks\LinkedTOC;

const BLOCK_NAME = 'shiro/linked-toc';

/**
 * Mimic the block template for creating a linked toc our of the `shiro/linked-toc-columns` block.
 */
//const BLOCK_TEMPLATE = [
//	'shiro/linked-toc-columns',
//
//];

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_action( 'the_content', __NAMESPACE__ . '\\filtered_content', 99 );

	add_filter('render_block', function($block_content, $block, $instance) {
		if ( $block['blockName'] === 'shiro/linked-toc' || $block['blockName'] === 'shiro/external-link' ) {
			return $block_content;
		}

		return $block_content;
	}, 10, 3 );
}

/**
 * Walk through the filtered content in the hierarchical order to be sure we are targeting the correct blocks.
 *
 * @param $content
 *
 * @return mixed
 */
function filtered_content( $content ) {
	// Check if the current page has the block, if so apply updates.
	if ( has_block( BLOCK_NAME, get_post()->post_content ) ) {
		$blocks = parse_blocks( get_post()->post_content );

		foreach( $blocks as $key => $block ) {
			if ( 'shiro/linked-toc-columns' !== $block['blockName'] ) {
				continue;
			}

			if ( empty( $block['innerBlocks'] ) ) {
				continue;
			}

			// Iterate our way to the link blocks.
			$columns_blocks = array_shift( $block['innerBlocks'] );
			// Verify we are are still on track. Should be at the columns.
			if ( $columns_blocks['blockName'] !== 'core/columns' ) {
				continue;
			}

			// Choose the left column, our "table of contents".
			$left_blocks = array_shift( $columns_blocks['innerBlocks'] );
			// Verify we are are still on track. Should be at the column.
			if ( $left_blocks['blockName'] !== 'core/column' ) {
				continue;
			}

			// Find the linked-toc block.
			$link_toc_block = array_shift( $left_blocks['innerBlocks'] );
			// Verify we are are still on track. Should be at the column.
			if ( $link_toc_block['blockName'] !== 'shiro/linked-toc' ) {
				continue;
			}

			// Iterate over the external links in the column.
			$link_blocks = $link_toc_block['innerBlocks'];
			foreach ( $link_blocks as $order => $link_block ) {
				if ( $link_block['blockName'] !== 'shiro/external-link' ) {
					continue;
				}

//				$matches = [];
/*				preg_match_all( '/<a(.*)?href=\"([^"]+)"*?>/', $inner_block['innerHTML'], $matches );*/
//
//				// There should be 3 matches.
//				if ( count( $matches ) === 3 ) {
//					continue;
//				}
//
//				// The href would be the 3rd match.
//				$href = array_pop( $matches );

				$link_block_doc = new \DOMDocument();
				$link_block_doc->loadHTML( $link_block['innerHTML'] );
				$classes = $link_block_doc->getElementsByTagName('div')[0]->getAttribute('class');
				$href = $link_block_doc->getElementsByTagName('a')[0]->getAttribute('href');

				// We are on the active link.
				if ( $href === get_permalink() ) {
					$link_block_doc->getElementsByTagName('div')[0]->setAttribute( 'class', array_merge( $classes, [ 'toc__link--active-page' ] ) );
				} else {
					$link_block_doc->getElementsByTagName('div')[0]->setAttribute( 'class', array_merge( $classes, [ 'toc__link' ] ) );
				}

				$link_block['innerHTML'] = $link_block['innerHTML'][0] = $link_block_doc->saveHTML();

				// Add the items back to the parent block.
				$link_blocks[ $order ] = $link_block;
				array_unshift( $link_toc_block, $blocks );
				array_unshift( $left_blocks, $link_toc_block );
				array_unshift( $columns_blocks, $left_blocks );
				$block[ $key ] = $columns_blocks;
			}

			return render_block( $blocks );
		}
	}

	return $content;
}
