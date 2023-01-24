<?php

namespace PublishPress\Permissions\UI;

// Plugin admin UI function not inherently tied to WordPress Dashboard framework
class PluginPage
{
    private static $instance = null;
    var $table;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new PluginPage();
        }
        
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_head', [$this, 'actAdminHead']);
    }

    public static function handleScreenOptions()
    {
        if (presspermit_is_REQUEST('wp_screen_options')) {
            if (isset($_REQUEST['wp_screen_options']['option']) && ('groups_per_page' == $_REQUEST['wp_screen_options']['option']) && isset($_REQUEST['wp_screen_options']['value'])) {
                global $current_user;
                update_user_option($current_user->ID, sanitize_key($_REQUEST['wp_screen_options']['option']), (int) $_REQUEST['wp_screen_options']['value']);
            }
        }
    }

    public static function icon()
    {
        echo '<div class="pp-icon"><img src="' . esc_url(PRESSPERMIT_URLPATH . '/common/img/publishpress-logo-icon.png') . '" alt="" /></div>';
    }

    public function actAdminHead()
    {
        global $pagenow;

        if (('upload.php' == $pagenow) && !defined('PRESSPERMIT_FILE_ACCESS_VERSION')
            && current_user_can('pp_manage_settings') && presspermit()->getOption('display_extension_hints')
        ) {
            require_once(PRESSPERMIT_CLASSPATH . '/UI/HintsMedia.php');
            HintsMedia::fileFilteringPromo();
        }

        if ('presspermit-groups' == presspermitPluginPage()) {
            // todo: eliminate redundancy with Groups::__construct()
            if (!empty($_REQUEST['action2']) && !is_numeric($_REQUEST['action2'])) {
                $action = presspermit_REQUEST_key('action2');

            } elseif (!empty($_REQUEST['action']) && !is_numeric($_REQUEST['action'])) {
                $action = presspermit_REQUEST_key('action');

            } elseif (!empty($_REQUEST['pp_action'])) {
                $action = presspermit_REQUEST_key('pp_action');
            } else {
                $action = '';
            }

            if ( ! in_array($action, ['delete', 'bulkdelete'])) {
                if (!$agent_type = presspermit_REQUEST_key('agent_type')) {
                    $agent_type = 'pp_group';
                }
            } else {
                $agent_type = '';
            }

            $agent_type = self::getAgentType($agent_type);

            $group_variant = self::getGroupVariant();

            if ( ! $this->table = apply_filters('presspermit_groups_list_table', false, $agent_type) ) {
                require_once(PRESSPERMIT_CLASSPATH . '/UI/GroupsListTable.php' );
                $this->table = new GroupsListTable(compact('agent_type', 'group_variant'));
            }

            add_screen_option(
                'per_page',
                
                ['label' => _x('Groups', 'groups per page (screen options)', 'press-permit-core'), 
                'default' => 20, 
                'option' => 'groups_per_page'
                ]
            );
        }

        add_action('in_admin_header', function() {do_action('presspermit_plugin_page_admin_header');}, 100);
    }

    public static function getAgentType($default_type = '') {
        if (!$_agent_type = presspermit_REQUEST_key('agent_type')) {
            $_agent_type = $default_type;
        }

        if (!$agent_type = sanitize_key(apply_filters('presspermit_query_group_type', $_agent_type))) {
            $agent_type = 'pp_group';
        }

        return $agent_type;
    }

    public static function getGroupVariant() {
        if (presspermit_empty_REQUEST('group_variant') && !presspermit_empty_REQUEST('s')) {
            if ($wp_http_referer = presspermit_REQUEST_var('_wp_http_referer')) {
                $matches = [];
                if (preg_match("/group_variant=([0-9a-zA-Z_\-]+)/", urldecode(esc_url_raw($wp_http_referer)), $matches)) {
                    if ($matches[1]) {
                        $group_variant = sanitize_key($matches[1]);
                    }
                }
            }
        } elseif (presspermit_empty_REQUEST('group_variant') && !current_user_can('edit_users')) {
            $group_variant = 'pp_group';
        }

        if (empty($group_variant)) {
            $group_variant = presspermit_REQUEST_key('group_variant');
        }

        return sanitize_key(apply_filters('presspermit_query_group_variant', $group_variant));
    }
}
