<?php
/**
 * Class to capture and store requested images during page load.
 *
 * @package wmfoundation
 */

namespace WMF\Images;

/**
 * Class for handling images to show credit after page load..
 */
class Credits {
	/**
	 * Requested image IDs.
	 *
	 * @var array
	 */
	public $image_ids = array();

	/**
	 * Holds image matches from preg_match_all().
	 *
	 * @var array
	 */
	public $image_matches = array();

	/**
	 * Post, post, or other request ID.
	 *
	 * Used to build cache.
	 *
	 * @var int
	 */
	public $request_id = 0;

	/**
	 * Credits constructor.
	 *
	 * @param int $request_id The ID for the request.
	 */
	public function __construct( $request_id ) {
		$this->request_id = $request_id;
		$this->image_ids  = $this->get_cache();

		if ( false === $this->image_ids ) {
			add_filter( 'image_downsize', array( $this, 'set_id' ), 10, 2 );
			add_filter( 'the_content', array( $this, 'set_images_from_content' ), 10, 2 );
		}
	}

	/**
	 * Gets the cache.
	 *
	 * @return bool|mixed
	 */
	public function get_cache() {
		$cache_key = md5( sprintf( 'wmf_image_credits_%s', $this->request_id ) );

		return wp_cache_get( $cache_key );
	}

	/**
	 * Adds the requested image ID to the list of IDs if not previously set.
	 *
	 * @param bool $bool     Override bool value used to replace downsize logic.
	 * @param int  $image_id The image ID.
	 *
	 * @return mixed
	 */
	public function set_id( $bool, $image_id ) {
		if ( ! in_array( $image_id, $this->image_ids, true ) ) {
			$this->image_ids[] = $image_id;
		}

		return $bool;
	}

	/**
	 * Does a preg_match_all to get image sources if there is no caption.
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	public function set_images_from_content( $content ) {
		preg_match_all( '/src="([^" >]+?)"(?!.*?\[\/caption\])/', $content, $this->image_matches );

		$this->process_image_matches();

		return $content;
	}

	/**
	 * Processes the matched images to get image IDs for credits.
	 */
	public function process_image_matches() {
		$urls = isset( $this->image_matches[1] ) ? $this->image_matches[1] : '';

		if ( empty( $urls ) || ! is_array( $urls ) ) {
			return;
		}

		foreach ( $urls as $url ) {
			$image_id = wpcom_vip_attachment_url_to_postid( $url );

			if ( empty( $image_id ) ) {
				continue;
			}

			$this->set_id( true, $image_id );
		}
	}

	/**
	 * Gets the image IDs.
	 *
	 * @return array
	 */
	public function get_ids() {
		remove_filter( 'image_downsize', array( $this, 'set_id' ), 10, 2 );

		return $this->image_ids;
	}
}
