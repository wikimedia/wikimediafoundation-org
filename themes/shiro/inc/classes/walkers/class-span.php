<?php
/**
 * Walker class for making span only output.
 *
 * @package shiro
 */

namespace WMF\Walkers;

/**
 * Simple walker to generate span only markup for menus.
 */
class Span extends \Walker_Nav_Menu {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '';
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '';
	}

	/**
	 * Starts the element output.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param \WP_Post $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param array    $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$classes = array();
		if ( ! empty( $item->classes ) ) {
			$classes = (array) $item->classes;
		}

		$active_class = '';
		if ( in_array( 'current-menu-item', $classes, true ) ) {
			$active_class = ' active';
		} elseif ( in_array( 'current-menu-parent', $classes, true ) ) {
			$active_class = ' active-parent';
		} elseif ( in_array( 'current-menu-ancestor', $classes, true ) ) {
			$active_class = ' active-ancestor';
		}

		$class = sprintf( ' class="link-list hover-highlight uppercase mar-right%s"', $active_class );

		$url = '';
		if ( ! empty( $item->url ) ) {
			$url = $item->url;
		}

		$output .= '<span' . $class . '><a href="' . $url . '">' . $item->title . '</a>';
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_el()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param \WP_Post $item   Page data object. Not used.
	 * @param int      $depth  Depth of page. Not Used.
	 * @param array    $args   An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= '</span>';
	}
}
