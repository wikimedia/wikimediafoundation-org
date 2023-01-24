<?php
namespace PublishPress\Permissions;

class Core {
    function __construct() {
        add_filter('presspermit_options', [$this, 'fltPressPermitOptions'], 15);

        add_action('init', function() { // late execution avoids clash with autoloaders in other plugins
            if (presspermitPluginPage()
            || (defined('DOING_AJAX') && DOING_AJAX && !empty($_REQUEST['action']) && (false !== strpos(sanitize_key($_REQUEST['action']), 'press-permit-core')))
            ) {
                if (!class_exists('\PublishPress\WordPressReviews\ReviewsController')) {
                    include_once PRESSPERMIT_ABSPATH . '/vendor/publishpress/wordpress-reviews/ReviewsController.php';
                }
        
                if (class_exists('\PublishPress\WordPressReviews\ReviewsController')) {
                    $reviews = new \PublishPress\WordPressReviews\ReviewsController(
                        'press-permit-core',
                        'PublishPress Permissions',
                        plugin_dir_url(PRESSPERMIT_FILE) . 'common/img/permissions-wp-logo.jpg'
                    );
        
                    add_filter('publishpress_wp_reviews_display_banner_press-permit-core', [$this, 'shouldDisplayBanner']);

                    $reviews->init();
                }
            }
        });
    }

    public function shouldDisplayBanner() {
        return presspermitPluginPage();
    }

    function fltPressPermitOptions($options) {
        $options['presspermit_display_extension_hints'] = true;
        return $options;
    }
}
