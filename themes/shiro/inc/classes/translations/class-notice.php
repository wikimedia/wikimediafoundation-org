<?php
/**
 * Adds publish actions and controls translation status.
 *
 * @package shiro
 */

namespace WMF\Translations;

/**
 * Adds publish actions and controls translation status.
 */
class Notice {

	/**
	 * The translation status taxonomy.
	 *
	 * @var string
	 */
	public $taxonomy = 'translation-status';

	/**
	 * Status terms array for the site.
	 *
	 * @var array
	 */
	public $status_terms = array();

	/**
	 * The post ID for the main site.
	 *
	 * @var int
	 */
	public $post_id = 0;

	/**
	 * Indicates there is a post in progress.
	 *
	 * @var bool
	 */
	public $has_post_in_progress = false;

	/**
	 * Notice constructor.
	 *
	 * @param int $post_id The post ID.
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Sets the status terms property.
	 */
	public function set_status_terms() {
		Flow::get_instance()->maybe_register_translation_status_terms();
		$this->status_terms = Flow::get_instance()->status_terms;
	}

	/**
	 * Checks to see if there is a linked translation in progress.
	 */
	public function check_progress() {
		if ( ! wmf_is_main_site() ) {
			return;
		}
		$remote_posts = wmf_get_translations( false, $this->post_id, 'post' );

		if ( empty( $remote_posts ) ) {
			return;
		}

		foreach ( $remote_posts as $remote_post ) {
			if ( wmf_is_main_site( $remote_post['site_id'] ) ) {
				continue;
			}

			switch_to_blog( $remote_post['site_id'] );

			$this->set_status_terms();

			if ( (int) $this->get_progress_term_id() === (int) $this->translation_status( $remote_post['content_id'] ) ) {
				$this->has_post_in_progress = true;
				restore_current_blog();
				return;
			}

			restore_current_blog();
		}
	}

	/**
	 * Gets the term ID for the in progress term.
	 *
	 * @return int
	 */
	public function get_progress_term_id() {
		return is_array( $this->status_terms['progress'] ) ? $this->status_terms['progress']['term_id'] : $this->status_terms['progress'];
	}

	/**
	 * Returns the post translation status label.
	 *
	 * @param int $post_id The remote post id.
	 *
	 * @return int
	 */
	public function translation_status( $post_id ) {
		$terms       = wp_get_post_terms( $post_id, $this->taxonomy );
		$status_term = isset( $terms[0] ) ? $terms[0] : '';

		return empty( $status_term->term_id ) ? 0 : $status_term->term_id;
	}

	/**
	 * Shows the notice if there is a post in progress.
	 */
	public function maybe_show_notice() {
		if ( $this->has_post_in_progress ) {
			printf(
				'<div class="notice notice-warning"><p>%s</p></div>',
				esc_html__( 'There is a translation in progress for this content.', 'shiro-admin' )
			);
		}
	}

	/**
	 * Adds column to the list table.
	 *
	 * @param array $columns List Table columns.
	 *
	 * @return array
	 */
	public static function cpt_columns( $columns ) {
		if ( wmf_is_main_site() ) {
			$columns['translation_progress'] = __( 'Translation Status', 'shiro-admin' );
		}
		return $columns;
	}

	/**
	 * Checks to see if there are translations in progress and outputs the correct icons.
	 *
	 * @param string $column  The column being displayed.
	 * @param int    $post_id The post ID.
	 */
	public static function cpt_column( $column, $post_id ) {
		if ( 'translation_progress' === $column ) {
			$notice = new static( $post_id );
			$notice->check_progress();
			$notice->show_table_progress();
		}
	}

	/**
	 * Shows a lock icon for content with translation in progress and unlock icon for the remaining content.
	 */
	public function show_table_progress() {
		if ( $this->has_post_in_progress ) {
			printf(
				'<abbr title="%1$s"><span class="dashicons dashicons-lock" style="color: #ff0000;"><span class="screen-reader-text">%1$s</span></span></abbr>',
				esc_html__( 'There is a translation in progress for this content.', 'shiro-admin' )
			);
		} else {
			printf(
				'<abbr title="%1$s"><span class="dashicons dashicons-unlock" style="color: #008000;"><span class="screen-reader-text">%1$s</span></span></abbr>',
				esc_html__( 'No translations in progress for this content.', 'shiro-admin' )
			);
		}
	}

}
