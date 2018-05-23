<?php
/**
 * Adds translation status "Needs Translation (new)" to all content on site clone.
 *
 * @package wmf-site-clone
 */

namespace WMFClone;

/**
 * Adds translation status "Needs Translation (new)" to all content on site clone.
 */
class Site {
	/**
	 * The translation status taxonomy.
	 *
	 * @var string
	 */
	public $taxonomy = 'translation-status';

	/**
	 * The term options key.
	 *
	 * @var string
	 */
	public $term_option = 'wmf_translation_status_terms';

	/**
	 * The new term ID.
	 *
	 * @var int
	 */
	public $term_id = 0;

	/**
	 * The post types that support translation status.
	 *
	 * @var array
	 */
	public $post_types = array( 'post', 'page', 'profile' );

	/**
	 * List of object IDs to add translation status to.
	 *
	 * @var array
	 */
	public $post_ids = array();

	/**
	 * The values for query to set translation status.
	 *
	 * @var array
	 */
	public $values = array();

	/**
	 * Status terms array for the site.
	 *
	 * @var array
	 */
	public $status_terms = array();

	/**
	 * Creates status terms if they were not previously set.
	 */
	public function register_translation_status_terms() {
		$terms = array(
			'new'      => __( 'Needs Translation (new)', 'wmfoundation' ),
			'update'   => __( 'Needs Translation (update)', 'wmfoundation' ),
			'progress' => __( 'In Progress', 'wmfoundation' ),
			'complete' => __( 'Complete', 'wmfoundation' ),
		);

		foreach ( $terms as $key => $term ) {
			$this->status_terms[ $key ] = wp_insert_term( $term, $this->taxonomy );
		}

		$this->term_id = is_array( $this->status_terms['new'] ) ? $this->status_terms['new']['term_id'] : $this->status_terms['new'];
		add_option( $this->term_option, $this->status_terms );
	}

	/**
	 * Sets the post IDs.
	 */
	public function set_posts() {
		global $wpdb;

		$this->post_ids = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type IN (%s, %s, %s)",
				$this->post_types[0],
				$this->post_types[1],
				$this->post_types[2]
			),
			ARRAY_N
		);
	}

	/**
	 * Adds the translation status in progress term.
	 */
	public function add_term() {
		foreach ( $this->post_ids as $post_id ) {
			wp_set_post_terms( $post_id[0], $this->term_id, $this->taxonomy );
		}
	}
}
