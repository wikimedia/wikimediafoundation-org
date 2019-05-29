<?php
/**
 * Profile sorter.
 *
 * @package shiro
 */

namespace WMF\Profiles;

/**
 * Sorts profiles array based on lastname field.
 */
class Sorter {
	/**
	 * The posts to sort.
	 *
	 * @var array
	 */
	public $posts = array();

	/**
	 * The posts that can be sorted.
	 *
	 * @var array
	 */
	public $sortable = array();

	/**
	 * The sorted posts.
	 *
	 * @var array
	 */
	public $sorted = array();

	/**
	 * The unsortale posts (will be appended to end of sorted posts.
	 *
	 * @var array
	 */
	public $unsortable = array();

	/**
	 * Sorter constructor.
	 *
	 * @param array $posts The posts to sort.
	 */
	public function __construct( $posts ) {
		$this->posts = $posts;
	}

	/**
	 * Initiates sorting and gets the sorted posts.
	 *
	 * @return array
	 */
	public function get_sorted() {
		$this->set_sortable();
		$this->sort();
		$this->append_unsortable();

		return empty( $this->sorted ) ? $this->posts : $this->sorted;
	}

	/**
	 * Sets the posts that are sortable.
	 */
	public function set_sortable() {
		if ( empty( $this->posts ) ) {
			return;
		}

		// Set an empty array just in case this class is being reused.
		$this->sortable   = array();
		$this->unsortable = array();

		foreach ( $this->posts as $post_id ) {
			$last_name = get_post_meta( $post_id, 'last_name', true );

			if ( empty( $last_name ) || ! is_string( $last_name ) ) {
				$this->unsortable[] = $post_id;
				continue;
			}

			$this->sortable[ $last_name ] = $post_id;
		}
	}

	/**
	 * Sorts the sortable property by array keys.
	 */
	public function sort() {
		ksort( $this->sortable );
	}

	/**
	 * Adds any unsortable items to the end of the sort array.
	 */
	public function append_unsortable() {
		$this->sorted = $this->sortable + $this->unsortable;
	}
}
