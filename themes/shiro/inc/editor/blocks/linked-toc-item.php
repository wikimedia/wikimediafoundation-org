<?php
/**
 * Additional functionality for the shiro/linked-toc-item block.
 *
 * This block is defined as a client side block in JS. This is additional functionality when rendering the block.
 *
 * @package shiro
 */

namespace WMF\Editor\Blocks\LinkedTOCItem;

const BLOCK_NAME = 'shiro/linked-toc-item';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_filter( 'render_block', __NAMESPACE__ . '\\update_toc_item', 10, 2 );
	add_filter( 'render_block', __NAMESPACE__ . '\\maybe_create_nested_toc', 20, 2 );
}

/**
 * Set the active item in the linked toc if the current url matches the permalink on the toc item.
 *
 * @param string $block_content The block content about to be appended.
 * @param array  $block The full block, including name and attributes.
 *
 * @return string
 */
function update_toc_item( string $block_content, array $block ) {
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $block_content;
	}

	// Possibly retrieve updated content that includes new classes or permalink.
	$helper = _toc_item_helper( $block_content, true );

	return is_null( $helper ) ? $block_content : $helper['content'];
}

/**
 * If a table of contents block is found, check to see if there is a linked table of contents in the parent block. If so,
 * add the linked toc block as the parent in the toc column with the contents of the table of contents block as a child under
 * the parent linked toc item.
 *
 * @param string $block_content The block content about to be appended.
 * @param array  $block The full block, including name and attributes.
 *
 * @return string Nested toc column or passed in block content.
 */
function maybe_create_nested_toc( string $block_content, array $block ) {
	// Only apply nesting if we find a toc block.
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
		if ( 'core/columns' !== $columns_blocks['blockName'] ) {
			continue;
		}

		// Choose the left column, our "table of contents".
		$left_blocks = array_shift( $columns_blocks['innerBlocks'] );
		// Verify we are are still on track. Should be at the column.
		if ( 'core/column' !== $left_blocks['blockName'] ) {
			continue;
		}

		// Find the linked-toc block.
		$link_toc_block = array_shift( $left_blocks['innerBlocks'] );
		// Verify we are are still on track. Should be at the column.
		if ( 'shiro/linked-toc' !== $link_toc_block['blockName'] ) {
			continue;
		}

		// Iterate over the links in the column.
		$link_blocks = $link_toc_block['innerBlocks'];

		// Do not continue if no link blocks are set on the parent link toc. Since there is only one linked toc allowed
		// fall out completely.
		if ( empty( $link_blocks ) ) {
			return $block_content;
		}

		foreach ( $link_blocks as $order => $link_block ) {
			if ( BLOCK_NAME !== $link_block['blockName'] ) {
				continue;
			}

			// Possibly retrieve updated content that includes new classes or update permalink.
			$helper = _toc_item_helper( $link_block['innerHTML'], false );
			if ( is_null( $helper ) ) {
				continue;
			}

			// Found the parent?
			if ( get_permalink() === $helper['href'] ) {
				// Append the original block content as a submenu after the item.
				$modified_block_content = sprintf( '%1$s<ul class="toc__nested">%2$s</ul>', $helper['content'], $block_content );
			} else {
				$modified_block_content = $helper['content'];
			}

			// Set the block properties to be the updated content.
			$link_block['innerHTML']       = $modified_block_content;
			$link_block['innerContent'][0] = $modified_block_content;

			// Add the items back on to the parent block stack.
			$link_blocks[ $order ] = $link_block;
		}

		// Re-assemble the blocks.
		$link_toc_block['innerBlocks'] = $link_blocks;
		array_unshift( $left_blocks['innerBlocks'], $link_toc_block );

		return render_block( $left_blocks );
	}

	return $block_content;
}

/**
 * Helper function to parse linked toc items; update permalinks and add active classes if the current post url matched the url specified
 * on the item.
 *
 * Return an array of potentially modified items in order to apply logic based on the changes that were made in this helper.
 *
 * @param string  $block_content The block content about to be appended.
 * @param boolean $page Identify if the current item is a page.
 *
 * @return array|null
 */
function _toc_item_helper( $block_content, $page ) {
	// We need to get the items class and href, so using a domdoc to confidently locate them.
	$link_block_doc = new \DOMDocument();
	$link_block_doc->loadHTML( $block_content, \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD );

	// Check for the link.
	if ( empty( $link_block_doc->getElementsByTagName( 'a' )->length ) ) {
		return null;
	}

	$a_element = $link_block_doc->getElementsByTagName( 'a' )[0];
	$classes   = $a_element->getAttribute( 'class' );
	$href      = $a_element->getAttribute( 'href' );
	$post_id   = $a_element->getAttribute( 'data-post-id' );

	if ( empty( $classes ) || empty( $href ) ) {
		return null;
	}

	if ( ! empty( $post_id ) ) {
		// Update href to value of post id to ensure we have the latest permalink.
		$href = get_permalink( $post_id );
		$a_element->setAttribute( 'href', $href );
	}

	// Check if we are on the active link.
	if ( get_permalink() === $href ) {
		$active_type = $page ? 'toc__link--active-page' : 'toc__link--active';
		$classes     = explode( ' ', $classes );
		$a_element->setAttribute( 'class', implode( ' ', array_merge( $classes, [ $active_type ] ) ) );
	}

	return [
		'classes' => $classes,
		'content' => $link_block_doc->saveHTML() ?? $block_content,
		'href'    => $href,
	];
}
