<?php
/**
 * Additional functionality for the shiro/linked-toc-item block.
 *
 * This block is defined in the block JS.
 */

namespace WMF\Editor\Blocks\LinkedTOCItem;

const BLOCK_NAME = 'shiro/linked-toc-item';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_filter( 'render_block', __NAMESPACE__ . '\\set_active_item', 10, 2 );
	add_filter( 'render_block', __NAMESPACE__ . '\\maybe_create_nested_toc', 20, 2 );
}

/**
 * Set the active item in the linked toc if the current url matches the permalink on the toc item.
 *
 * @param string $block_content The block content about to be appended.
 * @param array $block The full block, including name and attributes.
 *
 * @return string
 */
function set_active_item( string $block_content, array $block ) {
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $block_content;
	}

	// Apply the active class if the page url is active.
	$link_block_doc = new \DOMDocument();
	$link_block_doc->loadHTML( $block_content, \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD );

	$a_element = $link_block_doc->getElementsByTagName('a')[0];

	$classes = $a_element->getAttribute('class');
	$href    = $a_element->getAttribute('href');

	if ( empty( $classes ) || empty( $href ) ) {
		return $block_content;
	}
	$classes = explode( ' ', $classes );

	// If are on the landing page set the '#' as the active link.
	if ( $href == get_the_permalink() ) {
		$a_element->setAttribute( 'class', implode( array_merge( $classes, [ 'toc__link--active-page' ] ), ' ' ) );
	}

	$content = $link_block_doc->saveHTML();

	return is_string( $content ) ? $content : '';
}

/**
 * If a table of contents block is found, check to see if there is a linked table of contents in the parent block. If so,
 * add the linked toc block as the parent in the toc column with the contents of the table of contents block as a child under
 * the parent linked toc item.
 *
 * @param string $block_content The block content about to be appended.
 * @param array $block The full block, including name and attributes.
 *
 * @return string Nested toc column or passed in block content.
 */
function maybe_create_nested_toc( string $block_content, array $block ) {
	// if toc block is approached, check if parent has linked toc block.
	if ( 'shiro/toc' !== $block['blockName'] ) {
		return $block_content;
	}

	// Check if a parent page exists.
	$post_parent = get_post_parent();
	if ( empty( $post_parent ) ) {
		return $block_content;
	}

	// Check if parent has linked-toc block.
	if ( ! has_block( 'shiro/linked-toc', $post_parent ) ) {
		return $block_content;
	}

	// If so, use the linked toc block with the toc block content inserted into the corresponding space of the item.
	$parent_page_blocks = parse_blocks( get_post( $post_parent )->post_content );

	foreach ( $parent_page_blocks as $key => $parent_page_block ) {
		if ( 'shiro/linked-toc-columns' !== $parent_page_block['blockName'] ) {
			continue;
		}

		if ( empty( $parent_page_block['innerBlocks'] ) ) {
			continue;
		}

		// Iterate our way to the link blocks.
		$columns_blocks = array_shift( $parent_page_block['innerBlocks'] );
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

		// Iterate over the links in the column.
		$modified_blocks = false;
		$link_blocks = $link_toc_block['innerBlocks'];
		foreach ( $link_blocks as $order => $link_block ) {
			if ( $link_block['blockName'] !== 'shiro/linked-toc-item' ) {
				continue;
			}

			$link_block_doc = new \DOMDocument();
			$link_block_doc->loadHTML( $link_block['innerHTML'], \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD );

			$a_element = $link_block_doc->getElementsByTagName('a')[0];
			$classes   = $a_element->getAttribute('class');
			$href      = $a_element->getAttribute('href');

			if ( empty( $classes ) || empty( $href ) ) {
				continue;
			}
			$classes = explode( ' ', $classes );

			// We are on the active link.
			$is_active_item = ( $href === get_permalink() );
			if ( ! $is_active_item ) {
				continue;
			}

			$a_element->setAttribute( 'class', implode( array_merge( $classes, [ 'toc__link--active' ] ), ' ' ) );

			// Add the original block content.
			$modified_block_content = sprintf( '%1$s<ul class="toc__nested">%2$s</ul>', $link_block_doc->saveHTML(), $block_content );

			$link_block['innerHTML'] = $link_block['innerContent'][0] = $modified_block_content;

			// Add the items back on to the parent block stack.
			$link_blocks[ $order ] = $link_block;

			$modified_blocks = true;
		}

		if ( $modified_blocks ) {
			$link_toc_block['innerBlocks'] = $link_blocks;
			array_unshift($left_blocks['innerBlocks'], $link_toc_block );
		}

 		return render_block( $left_blocks );
	}

	return $block_content;
}
