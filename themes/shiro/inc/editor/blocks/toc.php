<?php
/**
 * Additional functionality for the shiro/toc block.
 *
 * This block is defined as a client side block in JS. This is additional functionality when rendering the block.
 *
 * @package shiro
 */

namespace WMF\Editor\Blocks\TOC;

use DOMDocument;
use DOMXPath;

const BLOCK_NAME = 'shiro/toc';
const PLACEHOLDER = '%MENU_PLACEHOLDER%';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_filter( 'render_block', __NAMESPACE__ . '\\render_toc_block', 10, 2 );
}

/**
 * Parse a post or page's raw content for header elements with IDs, and return
 * an array of those headings and their contents.
 *
 * @param string $content Post content (before block parsing).
 * @return array Array of headings with anchor IDs.
 */
function get_headings_from_post_content( string $content ) : array {
	// Block attributes stored in post markup are not available on their own
	// within PHP rendering code, even once the content is parsed as blocks.
	// DOMDocument is the most reliable tool to locate the values we want.
	$heading_block_doc = new DOMDocument();
	$heading_block_doc->loadHTML( $content );
	$xpath = new DOMXPath( $heading_block_doc );

	// Query for h2 and h3 elements that have an id attribute.
	// $matching_elements = $xpath->query( '//h2[@id] | //h3[@id]' );
	$matching_elements = $xpath->query( '//h2[@id]' ); // quickfix regarding to ticket #873.

	$headings = [];
	foreach ( $matching_elements as $header_element ) {
		// DOMDocument properties do not follow WP style guidelines.
		/* phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase */
		$headings[] = [
			'node'    => $header_element->tagName,
			'id'      => $header_element->getAttribute( 'id' ),
			'content' => $header_element->nodeValue,
		];
		/* eslint-enable */
	}

	return $headings;
}

/**
 * Convert a linear array of headings in the post into a nested structure.
 *
 * Assumes H2 is the topmost level, and discards any headings below $max_depth.
 * All headings within an H2 section are treated as the same level: that is,
 * [ h2, h3, h4 ] becomes [ { ...h2, children: [ h3, h4 ] } ] and not
 * [ { ...h2, children: [ { ...h3, children: [ h4 ] } ] } ].
 *
 * @param array  $headings  Array of [ node, anchor, content ] heading arrays.
 * @param string $max_depth Smallest level of heading to include.
 * @return array Array of headings nested by hierarchy.
 */
function headings_to_nested_list( array $headings, $max_depth = 'h3' ) : array {
	if ( empty( $headings ) ) {
		return [];
	}

	// Break headings into a naively nested structure where any heading
	// h2 or below is top level, and all others are nested within the
	// prior h2. The first heading is always treated as top level.
	// This should work properly in a well-ordered document, and be
	// resilient to poorly constructed heading hierarchies otherwise.
	$nested_headings = [];

	foreach ( $headings as $idx => $heading ) {
		if ( $idx === 0 || $heading['node'] < 'h3' ) {
			$nested_headings[] = array_merge( $heading, [ 'children' => [] ] );
			continue;
		}
		if ( $heading['node'] > $max_depth ) {
			continue;
		}
		$nested_headings[ array_key_last( $nested_headings ) ]['children'][] = $heading;
	}

	return $nested_headings;
}

/**
 * Output the ToC <ul> element given an array of headings.
 *
 * Expected structure:
 *
 * [
 *     [ 'id' => 'id-string', contents: 'Heading title', children: [] ],
 *     [ ... ]
 * ]
 *
 * @param array   $headings            List of headings.
 * @param boolean $render_nested_items Whether to render subitems.
 */
function render_headings_list( $headings, $render_nested_items = true ) : void {
	if ( empty( $headings ) ) {
		return;
	}
	?>
	<ul class="wp-block-shiro-toc table-of-contents toc">
		<?php foreach ( $headings as $heading ) : ?>
		<li class="toc__item">
			<a class="toc__link" href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['content'] ); ?></a>
			<?php if ( $render_nested_items && count( $heading['children'] ) ) : ?>
			<ul class="toc toc__nested">
				<?php foreach ( $heading['children'] as $nested_heading ) : ?>
				<li class="toc__item">
					<a class="toc__link" href="#<?php echo esc_attr( $nested_heading['id'] ); ?>">
						<?php echo esc_html( $nested_heading['content'] ); ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Render the table of contents block.
 *
 * @param string $block_content Saved block content.
 * @param array  $block         Block array.
 * @return string Rendered block content.
 */
function render_toc_block( string $block_content, array $block ) : string {
	if ( $block['blockName'] !== BLOCK_NAME ) {
		return $block_content;
	}

	if ( ! is_singular() ) {
		return '';
	}

	$headings = get_headings_from_post_content( get_post()->post_content ?? '' );
	$max_depth = ( $block['attrs']['includeH3s'] ?? false ) ? 'h3' : 'h2';
	$max_depth = 'h2'; // quickfix regarding to ticket #873.
	$headings = headings_to_nested_list( $headings, $max_depth );

	if ( empty( $headings ) ) {
		return '';
	}

	ob_start();
	?>
	<nav
		class="toc-nav"
		data-backdrop="inactive"
		data-dropdown="toc-nav"
		data-dropdown-content=".toc"
		data-dropdown-status="uninitialized"
		data-dropdown-toggle=".toc__button"
		data-sticky="false"
		data-toggleable="yes"
		data-trap="inactive"
		data-visible="false"
	>
		<h2 class="toc__title screen-reader-text">
			<?php esc_html_e( 'Table of Contents', 'shiro' ); ?>
		</h2>
		<button aria-expanded="false" class="toc__button" hidden>
			<span class="btn-label-a11y">
				<?php esc_html_e( 'Navigate within this page.', 'shiro' ); ?>
			</span>
			<span class="btn-label-active-item">
				<?php echo esc_html( $headings[0]['content'] ?? __( 'Toggle menu', 'shiro' ) ); ?>
			</span>
		</button>
		<ul class="wp-block-shiro-toc table-of-contents toc">
			<?php render_headings_list( $headings ); ?>
		</ul>
	</nav>
	<?php
	return (string) ob_get_clean();
}
