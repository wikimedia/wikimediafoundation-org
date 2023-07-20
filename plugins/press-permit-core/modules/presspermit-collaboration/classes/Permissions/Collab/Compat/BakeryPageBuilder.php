<?php
namespace PublishPress\Permissions\Collab\Compat;

class BakeryPageBuilder 
{
    function __construct() {
        add_filter('get_terms_args', [$this, 'fltGetTermsArgs'], 10, 2);
    }

    public function fltGetTermsArgs($args, $taxonomies)
    {
        if (defined('DOING_AJAX') && DOING_AJAX && !presspermit_empty_REQUEST('action') && presspermit_REQUEST_key_match('action', 'vc_get')) {
            $args['required_operation'] = 'read';

            add_filter('presspermit_apply_term_count_filters', function() {
                return false;
            });
        }

        return $args;
    }
}
