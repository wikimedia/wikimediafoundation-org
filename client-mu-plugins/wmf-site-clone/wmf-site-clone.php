<?php
/**
 * Adds translation status "Needs Translation (new)" to all content on site clone.
 *
 * @package wmf-site-clone
 */

namespace WMFClone;

class Site_Clone {
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
	 * Triggers the logic to add the new translation status to all posts.
	 *
	 * @param array $context The site context.
	 */
	public static function mlp_duplicated_blog( $context ) {

		$site_clone = new static();

		switch_to_blog( $context['new_blog_id'] );

		$site_clone->register_translation_status_terms();
		$site_clone->set_posts();
		$site_clone->build_query();
		$site_clone->do_query();

		restore_current_blog();
	}

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
	 * Sets the post terms.
	 */
	public function set_posts() {
		global $wpdb;

		$query = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN (%s, %s, %s)", $this->post_types[0], $this->post_types[1], $this->post_types[2] );

		$this->post_ids = $wpdb->get_results( $query, ARRAY_N );
	}

	/**
	 * Builds the query for inserting multiple rows.
	 */
	public function build_query() {
		global $wpdb;

		foreach ( $this->post_ids as $post_id ) {
			$this->values[] = $wpdb->prepare( "(%d, %d, %d)", $post_id[0], $this->term_id, 0 );
		}
	}

	/**
	 * Does the query that inserts the term relationship rows and updates the term object count.
	 */
	public function do_query() {
		global $wpdb;
		$wpdb->query( "INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join( ',', $this->values ) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)" );
		$wpdb->update( $wpdb->term_taxonomy, array( 'count' => count( $this->post_ids ) ), array( 'term_id' => $this->term_id ), array( '%s' ) );
	}
}

add_action( 'mlp_duplicated_blog', array( __NAMESPACE__ . '\Site_Clone', 'mlp_duplicated_blog' ) );