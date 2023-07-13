<?php

namespace PublishPress;

class PermissionsHooksAdmin
{
    private $config_updated = false;

    public function __construct()
    {
        add_action('presspermit_duplicate_module', [$this, 'duplicateModule'], 10, 2);

        add_filter('presspermit_pattern_roles', [$this, 'fltPatternRoles']);

        add_action('admin_menu', [$this, 'actSettingsPageMaybeRedirect'], 999);

        if (defined('SSEO_VERSION')) {
            require_once(PRESSPERMIT_CLASSPATH . '/Compat/EyesOnlyAdmin.php');
            new Permissions\Compat\EyesOnlyAdmin();
        }

        // make sure empty terms are included in quick search results in "Set Specific Permissions" term selection metaboxes
        if (PWP::isAjax('pp-menu-quick-search') && !empty($_REQUEST['action'])) {
            require_once(PRESSPERMIT_CLASSPATH.'/UI/ItemsMetabox.php' );
            add_action('wp_ajax_' . presspermit_REQUEST_key('action'), ['\PublishPress\Permissions\UI\ItemsMetabox', 'ajax_menu_quick_search'], 1);
        }

        // thanks to GravityForms for the nifty dismissal script
        if (!empty($_SERVER['PHP_SELF']) && in_array(basename($_SERVER['PHP_SELF']), ['admin.php', 'admin-ajax.php'])) {
            add_action('wp_ajax_pp_dismiss_msg', [$this, 'dashboardDismissMsg']);
        }

        if (defined('PUBLISHPRESS_MULTIPLE_AUTHORS_VERSION')) {
            add_filter('authors_default_author', [$this, 'fltAuthorsPreventPostCreationLockout'], 99, 2);
            add_action('save_post', [$this, 'actAuthorsPreventPostUpdateLockout'], 1, 3);
        }

        add_action('presspermit_admin_ui', [$this, 'act_revisions_dependency']);

        add_filter('presspermit_exception_item_update_hooks', function($var) {return true;});
        add_filter('presspermit_exception_item_insertion_hooks', function($var) {return true;});
        add_filter('presspermit_exception_item_deletion_hooks', function($var) {return true;});

        add_action('presspermit_exception_items_updated', [$this, 'actPluginSettingsUpdated']);
        add_action('presspermit_inserted_exception_item', [$this, 'actPluginSettingsUpdated']);
        add_action('pp_inserted_exception_item', [$this, 'actPluginSettingsUpdated']);
        add_action('presspermit_removed_exception_items', [$this, 'actPluginSettingsUpdated']);

        add_action('presspermit_trigger_cache_flush', [$this, 'wpeCacheFlush']);
        add_action('presspermit_activate', [$this, 'actPluginSettingsUpdated']);
        add_action('shutdown', [$this, 'actConfigUpdateFollowup']);
    }

    public function init()
    {
        if (presspermit()->isPro()) {
            require_once(PRESSPERMIT_PRO_ABSPATH . '/includes-pro/pro-maint.php');
            Permissions\PressPermitMaint::adminRedirectCheck();
        }

        require_once(PRESSPERMIT_CLASSPATH . '/CapabilityFiltersAdmin.php');
        new Permissions\CapabilityFiltersAdmin();

        if (presspermitPluginPage()) {
            require_once(PRESSPERMIT_CLASSPATH . '/UI/PluginPage.php');
            Permissions\UI\PluginPage::instance();
        }

        if (!presspermit_empty_POST() || !presspermit_empty_REQUEST('action') || !presspermit_empty_REQUEST('action2') || !presspermit_empty_REQUEST('pp_action')) {
            require_once(PRESSPERMIT_CLASSPATH . '/UI/Handlers/Admin.php');
            Permissions\UI\Handlers\Admin::handleRequest();
        }

        add_action('wp_loaded', [$this,'actLoadAjaxHandler'], 20);

        if (!presspermit_empty_POST('presspermit_submit') || !presspermit_empty_POST('presspermit_defaults') || !presspermit_empty_POST('pp_role_usage_defaults')) {
            // For 'settings' admin panels, handle updated options right after current_user load (and before pp_init).
            // By then, check_admin_referer is available, but PP config and WP admin menu has not been loaded yet.
            require_once(PRESSPERMIT_CLASSPATH . '/UI/Handlers/Settings.php');
            new Permissions\UI\Handlers\Settings();
        }

        if (!presspermit_empty_REQUEST('presspermit_refresh_updates') || !presspermit_empty_REQUEST('pp_renewal')) {
            if (!current_user_can('pp_manage_settings')) {
                wp_die(esc_html(PWP::__wp('Cheatin&#8217; uh?')));
            }
    
            if (!presspermit_empty_REQUEST('presspermit_refresh_updates')) {
                delete_site_transient('update_plugins');
                delete_option('_site_transient_update_plugins');
                wp_update_plugins();
                wp_redirect(admin_url('admin.php?page=presspermit-settings&presspermit_refresh_done=1'));
                exit;
            }
    
            if (!presspermit_empty_REQUEST('pp_renewal')) {
                if (presspermit()->isPro()) {
                    include_once(PRESSPERMIT_PRO_ABSPATH . '/includes-pro/pro-renewal-redirect.php');
                } else {
                    include_once(PRESSPERMIT_ABSPATH . '/includes/renewal-redirect.php');
                }
    
                exit;
            }
        }

        if (presspermit_is_GET('pp_agent_search')) {
            require_once(PRESSPERMIT_CLASSPATH . '/UI/AgentsAjax.php');
            new Permissions\UI\AgentsAjax();
            exit;
        }
    }

    public function actPluginSettingsUpdated() {
        $this->config_updated = true;
    }

    public function actConfigUpdateFollowup() {
        if ($this->config_updated) {
            $this->wpeCacheFlush();
        }
    }

    /**
     * Full WP Engine cache flush (Hold for possible future use as needed)
     *
     * Based on WP Engine Cache Flush by Aaron Holbrook
     * https://github.org/a7/wpe-cache-flush/
     * http://github.org/a7/
     */
    public function wpeCacheFlush() {
        // Don't cause a fatal if there is no WpeCommon class
        if ( ! class_exists( 'WpeCommon' ) ) {
            return false;
        }

        if ( function_exists( 'WpeCommon::purge_memcached' ) ) {
            \WpeCommon::purge_memcached();
        }

        if ( function_exists( 'WpeCommon::clear_maxcdn_cache' ) ) {
            \WpeCommon::clear_maxcdn_cache();
        }

        if ( function_exists( 'WpeCommon::purge_varnish_cache' ) ) {
            \WpeCommon::purge_varnish_cache();
        }

        global $wp_object_cache;
        // Check for valid cache. Sometimes this is broken -- we don't know why! -- and it crashes when we flush.
        // If there's no cache, we don't need to flush anyway.

        if ( !empty($wp_object_cache) && is_object( $wp_object_cache ) ) {
            @wp_cache_flush();
        }
    }

    public function actLoadAjaxHandler()
    {
        foreach (['item', 'agent_roles', 'agent_exceptions', 'agent_permissions', 'user', 'settings', 'items_metabox'] as $ajax_type) { // todo: term_ui ?
            if (isset($_REQUEST["pp_ajax_{$ajax_type}"])) {
                $class_name = str_replace('_', '', ucwords( $ajax_type, '_') ) . 'Ajax';
                
                $class_parent = ( in_array($class_name, ['ItemAjax','UserAjax']) ) ? 'Dashboard' : '';
                
                $require_path = ( $class_parent ) ? "{$class_parent}/" : '';
                require_once(PRESSPERMIT_CLASSPATH . "/UI/{$require_path}{$class_name}.php");
                
                $load_class = "\\PublishPress\Permissions\UI\\";
                $load_class .= ($class_parent) ? $class_parent . "\\" . $class_name : $class_name;

                new $load_class();

                exit;
            }
        }
    }

    public function duplicateModule($ext_slug, $ext_folder)
    {
        presspermit()->admin()->errorNotice('duplicate_module', ['module_slug' => $ext_slug, 'module_folder' => $ext_folder]);
    }

    public function fltPatternRoles($roles)
    {
        $roles['subscriber']->labels = (object)['name' => esc_html__('Subscribers', 'press-permit-core'), 'singular_name' => esc_html__('Subscriber', 'press-permit-core')];
        $roles['contributor']->labels = (object)['name' => esc_html__('Contributors', 'press-permit-core'), 'singular_name' => esc_html__('Contributor', 'press-permit-core')];
        $roles['author']->labels = (object)['name' => esc_html__('Authors', 'press-permit-core'), 'singular_name' => esc_html__('Author', 'press-permit-core')];
        $roles['editor']->labels = (object)['name' => esc_html__('Editors', 'press-permit-core'), 'singular_name' => esc_html__('Editor', 'press-permit-core')];

        return $roles;
    }

    function act_revisions_dependency() {
        global $pagenow;

        if (defined("PUBLISHPRESS_REVISIONS_VERSION") || defined('REVISIONARY_VERSION')) {
            if (!defined('PRESSPERMIT_COLLAB_VERSION')) {
                if (!presspermitPluginPage() && presspermit_is_REQUEST('page', ['revisionary-q', 'revisionary-settings']) && ('edit.php' !== $pagenow)) {
                    return;
                }

                $msg = current_user_can('pp_manage_settings')
                ? sprintf(
                    esc_html__('Please %senable the Collaborative Publishing module%s for PublishPress Revisions integration.', 'press-permit-core'),
                    '<a href="' . admin_url('admin.php?page=presspermit-settings') . '" style="text-decoration:underline">',
                    '</a>'
                )
                : esc_html__('PublishPress Revisions integration requires the Collaborative Publishing module. Please notify your Administrator.', 'press-permit-core');

                presspermit()->admin()->notice($msg);
            }
        }
    }

    // For old extensions linking to page=pp-settings.php, redirect to page=presspermit-settings, preserving other request args
    public function actSettingsPageMaybeRedirect()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return;
        }

        foreach ([
                     'pp-settings' => 'presspermit-settings',
                     'pp-groups' => 'presspermit-groups',
                     'pp-group-new' => 'presspermit-group-new',
                     'pp-users' => 'presspermit-users',
                     'pp-edit-permissions' => 'presspermit-edit-permissions',
                 ] as $old_slug => $new_slug) {
            if (
                strpos(esc_url_raw($_SERVER['REQUEST_URI']), "page=$old_slug")
                && (false !== strpos(esc_url_raw($_SERVER['REQUEST_URI']), 'admin.php'))
            ) {
                global $submenu;

                // Don't redirect if pp-settings is registered by another plugin or theme
                foreach (array_keys($submenu) as $i) {
                    foreach (array_keys($submenu[$i]) as $j) {
                        if (isset($submenu[$i][$j][2]) && ($old_slug == $submenu[$i][$j][2])) {
                            return;
                        }
                    }
                }

                $arr_url = wp_parse_url(esc_url_raw($_SERVER['REQUEST_URI']));
                wp_redirect(admin_url('admin.php?' . str_replace("page=$old_slug", "page=$new_slug", $arr_url['query'])));
                exit;
            }
        }
    }

    public function dashboardDismissMsg()
    {
        $dismissals = get_option('presspermit_dismissals');
        if (!is_array($dismissals)) {
            $dismissals = [];
        }

        $msg_id = (isset($_REQUEST['msg_id'])) ? sanitize_key($_REQUEST['msg_id']) : 'post_blockage_priority';
        $dismissals[$msg_id] = true;
        update_option('presspermit_dismissals', $dismissals);
    }

    // Prevent users lacking edit_others capability from being locked out of their newly created post due to Authors' "default author for new posts" setting
    function fltAuthorsPreventPostCreationLockout($default_author, $post) {
        global $current_user;

        if (presspermit()->isAdministrator()) {
            return $default_author;
        }
        
        if ($post_type = PWP::findPostType()) {
            if ($type_obj = get_post_type_object($post_type)) {
                if (!empty($type_obj->cap->edit_others_posts) && !current_user_can($type_obj->cap->edit_others_posts)) {
                    if (apply_filters('presspermit_override_default_author', true, ['post_type' => $post_type])) {
                        return $current_user->ID;
                    }
                }
            }
        }

        return $default_author;
    }

    function actAuthorsPreventPostUpdateLockout($post_id, $post, $update) {
        global $current_user;
        
        if (!$update
        || presspermit()->isAdministrator() 
        || !function_exists('get_multiple_authors') 
        || !function_exists('is_multiple_author_for_post')
        || !presspermit_is_POST('authors')
        || !apply_filters('presspermit_maybe_override_authors_change', true, $post)
        ) {
            return;
        }
        
        if ($post_type = PWP::findPostType()) {
            if ($type_obj = get_post_type_object($post_type)) {
                if (!empty($type_obj->cap->edit_others_posts) 
                && empty($current_user->allcaps[$type_obj->cap->edit_others_posts])
                && is_multiple_author_for_post($current_user, $post_id)
                && method_exists('MultipleAuthors\Classes\Objects\Author', 'get_by_user_id')
                ) {
                    if (!$current_author = \MultipleAuthors\Classes\Objects\Author::get_by_user_id($current_user->ID)) {
                        return;
                    }

                    $authors = presspermit_POST_var('authors');

                    if (!in_array($current_author->term_id, $authors)) {
                        if (apply_filters('presspermit_override_authors_change', true, $post)) {
                            $_POST['authors'] = array_merge([strval($current_author->term_id)], array_map('sanitize_key', $authors));
                        }
                    }
                }
            }
        }
    }
}
