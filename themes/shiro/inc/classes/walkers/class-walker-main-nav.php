<?php
/**
 * Class to provide a custom walker to navigation menus.
 *
 * @package shiro
 */

namespace WMF\Walkers;

/**
 * Custom walker class for the primary navigation.
 */
class Walker_Main_Nav extends \Walker_Nav_Menu {

	// Private variable for the current menu item.
	private $currentItemID;
	
	/**
	 * Starts the list before the elements are added.
	 *
	 * Adds classes to the unordered list sub-menus.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent

		$classes = array(
			'sub-menu',
			'nav-sub-menu-'. $this->currentItemID,
		);
		$class_names = implode( ' ', $classes );
	
		// Build HTML for output.
		$output .= "\n" . $indent . '<ul class="' . esc_attr( $class_names ) . '" data-dropdown-content="nav-sub-menu-'. $this->currentItemID . '">' . "\n";
	}

	/**
	 * Start the element output.
	 *
	 * Adds main/sub-classes to the list items and links.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

		// Set the current item.
		$this->currentItemID = $item->ID;

		// Passed classes.
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// Remove "has-children" class from second level items.
		$key = array_search( 'menu-item-has-children', $classes, true );

		if ( $depth > 0 && $key !== false ) {
			unset( $classes[$key] );
		}

		// Apply filters and prepare classes for use.
		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

		// Build HTML.
		$active_classes = [ 'current-menu-item', 'current-menu-ancestor' ];
		$output .= 
			$indent
			. '<li id="nav-menu-item-'. $item->ID . '"'
			. ' class="' . $class_names . '"'
			. ( in_array( 'menu-item-has-children', $classes, true ) ?
				' data-dropdown="nav-sub-menu-' . $item->ID . '"'
				. ' data-dropdown-content=".nav-sub-menu-'. $item->ID . '"'
				. ' data-dropdown-toggle=".nav-sub-menu-button-'. $item->ID . '"'
				. ' data-visible="' . ( count( array_intersect( $active_classes, $classes ) ) > 0 ? 'yes' : 'no' ) . '"'
				. ' data-toggleable="no"'
			: '' )
			. '>';

		// Apply nav menu item filter.
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		// Link attributes.
		$attributes = ! empty( $item->url ) ? ' href="' . esc_url( $item->url ) . '"' : '';

		// Build HTML output and pass through the proper filter.
		$item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
			$args->before,
			$attributes,
			$args->link_before,
			apply_filters( 'the_title', $item->title, $item->ID ),
			$args->link_after,
			$args->after
		);

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

		if ( in_array( 'menu-item-has-children', $classes, true ) ) {
			$label = __( 'Expand submenu for ', 'shiro' ) . apply_filters( 'the_title', $item->title, $item->ID );
			$output .= 
			'<button class="menu-item__expand nav-sub-menu-button-'. $item->ID . '"'
			. ' hidden'
			. ' aria-label="' . esc_attr( $label ) . '"'
			. ' aria-expanded="' . ( count( array_intersect( $active_classes, $classes ) ) > 0 ? 'true' : 'false' ) . '"'
			. ' data-dropdown-toggle="nav-sub-menu-'. $item->ID . '">'
			. '<span class="btn-label-a11y">'
			. esc_html( $label )
			. '</span></button>';
		}
	}
}
