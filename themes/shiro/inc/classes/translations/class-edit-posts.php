<?php
/**
 * Adds filter dropdown to posts, pages, and profiles to limit returned items by translation status.
 *
 * @package shiro
 */

namespace WMF\Translations;

/**
 * Adds filter dropdown to posts, pages, and profiles to limit returned items by translation status.
 */
class Edit_Posts {
	/**
	 * The current translation status.
	 *
	 * @var int|string
	 */
	public $translation_status;

	/**
	 * Edit_Posts constructor.
	 */
	public function __construct() {
		$this->translation_status = isset( $_GET['translation-status'] ) ? sanitize_title( wp_unslash( $_GET['translation-status'] ) ) : ''; // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected,WordPress.CSRF.NonceVerification.NoNonceVerification
	}

	/**
	 * Conditionally invokes the translation_status_dropdown method.
	 *
	 * @param string $post_type The post type.
	 * @param string $which     The context.
	 */
	public static function restrict_manage_posts( $post_type, $which ) {
		if ( wmf_is_main_site() ) {
			return;
		}

		if ( 'top' !== $which ) {
			return;
		}

		$edit_posts = new static();

		$edit_posts->translation_status_dropdown( $post_type );
	}

	/**
	 * Displays a Translation Status drop-down for filtering on the Posts list table.
	 *
	 * @param string $post_type Post type slug.
	 */
	protected function translation_status_dropdown( $post_type ) {
		$tax = 'translation-status';

		if ( is_object_in_taxonomy( $post_type, $tax ) ) {
			$dropdown_options = array(
				'show_option_all' => __( 'All Translation Statuses', 'shiro-admin' ),
				'hide_empty'      => 0,
				'hierarchical'    => 1,
				'show_count'      => 0,
				'orderby'         => 'name',
				'selected'        => $this->translation_status,
				'name'            => $tax,
				'taxonomy'        => $tax,
				'value_field'     => 'slug',
			);

			echo '<label class="screen-reader-text" for="' . esc_attr( $tax ) . '">' . esc_html__( 'Filter by translation status.', 'shiro-admin' ) . '</label>';
			wp_dropdown_categories( $dropdown_options );
		}
	}

}
