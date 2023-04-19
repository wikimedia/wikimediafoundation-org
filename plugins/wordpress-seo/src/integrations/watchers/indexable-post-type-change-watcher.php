<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Indexation_Action;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Conditionals\Not_Admin_Ajax_Conditional;
use Yoast\WP\SEO\Config\Indexing_Reasons;
use Yoast\WP\SEO\Helpers\Indexing_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Integrations\Cleanup_Integration;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Post type change watcher.
 */
class Indexable_Post_Type_Change_Watcher implements Integration_Interface {

	/**
	 * The indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	protected $indexing_helper;

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Holds the Post_Type_Helper instance.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type_helper;

	/**
	 * The notifications center.
	 *
	 * @var Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Not_Admin_Ajax_Conditional::class, Admin_Conditional::class, Migrations_Conditional::class ];
	}

	/**
	 * Indexable_Post_Type_Change_Watcher constructor.
	 *
	 * @param Options_Helper            $options             The options helper.
	 * @param Indexing_Helper           $indexing_helper     The indexing helper.
	 * @param Post_Type_Helper          $post_type_helper    The post_typehelper.
	 * @param Yoast_Notification_Center $notification_center The notification center.
	 */
	public function __construct(
		Options_Helper $options,
		Indexing_Helper $indexing_helper,
		Post_Type_Helper $post_type_helper,
		Yoast_Notification_Center $notification_center
	) {
		$this->options             = $options;
		$this->indexing_helper     = $indexing_helper;
		$this->post_type_helper    = $post_type_helper;
		$this->notification_center = $notification_center;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'check_post_types_public_availability' ] );
	}

	/**
	 * Checks if one or more post types change visibility.
	 *
	 * @return void
	 */
	public function check_post_types_public_availability() {

		// We have to make sure this is just a plain http request, no ajax/REST.
		if ( \wp_is_json_request() ) {
			return;
		}

		$public_post_types            = \array_keys( $this->post_type_helper->get_public_post_types() );
		$last_known_public_post_types = $this->options->get( 'last_known_public_post_types', [] );

		// Initializing the option on the first run.
		if ( empty( $last_known_public_post_types ) ) {
			$this->options->set( 'last_known_public_post_types', $public_post_types );
			return;
		}

		// We look for new public post types.
		$newly_made_public_post_types = \array_diff( $public_post_types, $last_known_public_post_types );
		// We look for post types that from public have been made private.
		$newly_made_non_public_post_types = \array_diff( $last_known_public_post_types, $public_post_types );

		// Nothing to be done if no changes has been made to post types.
		if ( empty( $newly_made_public_post_types ) && ( empty( $newly_made_non_public_post_types ) ) ) {
			return;
		}

		// Update the list of last known public post types in the database.
		$this->options->set( 'last_known_public_post_types', $public_post_types );

		// There are new post types that have been made public.
		if ( ! empty( $newly_made_public_post_types ) ) {

			// Force a notification requesting to start the SEO data optimization.
			\delete_transient( Indexable_Post_Indexation_Action::UNINDEXED_COUNT_TRANSIENT );
			\delete_transient( Indexable_Post_Indexation_Action::UNINDEXED_LIMITED_COUNT_TRANSIENT );

			$this->indexing_helper->set_reason( Indexing_Reasons::REASON_POST_TYPE_MADE_PUBLIC );

			$this->maybe_add_notification();
		}

		// There are post types that have been made private.
		if ( ! empty( $newly_made_non_public_post_types ) ) {
			// Schedule a cron job to remove all the posts whose post type has been made private.
			$cleanup_not_yet_scheduled = ! \wp_next_scheduled( Cleanup_Integration::START_HOOK );
			if ( $cleanup_not_yet_scheduled ) {
				\wp_schedule_single_event( ( \time() + ( \MINUTE_IN_SECONDS * 5 ) ), Cleanup_Integration::START_HOOK );
			}
		}
	}

	/**
	 * Decides if a notification should be added in the notification center.
	 *
	 * @return void
	 */
	private function maybe_add_notification() {
		$notification = $this->notification_center->get_notification_by_id( 'post-types-made-public' );
		if ( \is_null( $notification ) ) {
			$this->add_notification();
		}
	}

	/**
	 * Adds a notification to be shown on the next page request since posts are updated in an ajax request.
	 *
	 * @return void
	 */
	private function add_notification() {
		$message = \sprintf(
			/* translators: 1: Opening tag of the link to the Search appearance settings page, 2: Link closing tag. */
			\esc_html__( 'It looks like you\'ve added a new type of content to your website. We recommend that you review your %1$sSearch appearance settings%2$s.', 'wordpress-seo' ),
			'<a href="' . \esc_url( \admin_url( 'admin.php?page=wpseo_titles#top#post-types' ) ) . '">',
			'</a>'
		);

		$notification = new Yoast_Notification(
			$message,
			[
				'type'         => Yoast_Notification::WARNING,
				'id'           => 'post-types-made-public',
				'capabilities' => 'wpseo_manage_options',
				'priority'     => 0.8,
			]
		);

		$this->notification_center->add_notification( $notification );
	}
}
