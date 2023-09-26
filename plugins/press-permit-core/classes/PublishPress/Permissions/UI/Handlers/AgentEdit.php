<?php

namespace PublishPress\Permissions\UI\Handlers;

class AgentEdit
{
    public function __construct() {
        global $current_user;
        
        require_once(PRESSPERMIT_CLASSPATH . '/DB/GroupUpdate.php');

        $action = presspermit_REQUEST_key('action');

        $url = apply_filters('presspermit_groups_base_url', 'admin.php');
        $redirect = $err = false;

        if (!$agent_type = presspermit_REQUEST_key('agent_type')) {
            return;
        }

        $pp = presspermit();

        switch ($action) {
            case 'update':
                $agent_id = presspermit_REQUEST_int('agent_id');
                check_admin_referer('pp-update-group_' . $agent_id);

                if (!$pp->groups()->userCan('pp_edit_groups', $agent_id, $agent_type))
                    wp_die(esc_html__('You are not permitted to do that.', 'press-permit-core'));

                if ($pp->groups()->groupTypeEditable($agent_type)) {
                    $group = $pp->groups()->getGroup($agent_id, $agent_type);

                    $retval = $this->triggerGroupEdit($agent_id, $agent_type);
                } else {
                    $retval = true;
                }

                do_action('presspermit_edited_group', $agent_type, $agent_id);

                if (!empty($retval) && !is_wp_error($retval)) {
                    $redirect = "$url?page=presspermit-edit-permissions&agent_id=$agent_id&agent_type=$agent_type&updated=1";
                }
                break;

            case 'pp_updateclone':
                if (current_user_can('pp_assign_roles') && $pp->admin()->bulkRolesEnabled() && presspermit_is_REQUEST('pp_select_role')) {
                    $agent_id = presspermit_REQUEST_int('agent_id');
                    require_once(PRESSPERMIT_CLASSPATH . '/DB/Cloner.php');
                    \PublishPress\Permissions\DB\Cloner::clonePermissions(
                        'pp_group', 
                        $agent_id, 
                        presspermit_REQUEST_key('pp_select_role')
                    );
                    
                    $redirect = "$url?page=presspermit-edit-permissions&agent_id=$agent_id&agent_type=$agent_type&updated=1&pp_cloned=1";
                }

                break;

            case 'pp_updateroles':
                $agent_id = presspermit_REQUEST_int('agent_id');
                check_admin_referer('pp-update-roles_' . $agent_id, '_pp_nonce_roles');

                if (current_user_can('pp_assign_roles') && $pp->admin()->bulkRolesEnabled()) {
                    $this->editGroupRoles($agent_id, $agent_type);
                }

                // cludged update of group members on role selection, due to inability to put them in the same form
                if (
                    $agent_id && $pp->groups()->groupTypeEditable($agent_type)
                    && $pp->groups()->userCan('pp_edit_groups', $agent_id, $agent_type)
                ) {
                    $this->triggerGroupEdit($agent_id, $agent_type);
                }

                update_user_option($current_user->ID, 'pp-permissions-tab', 'pp-add-roles');

                $redirect = "$url?page=presspermit-edit-permissions&agent_id=$agent_id&agent_type=$agent_type&updated=1&pp_roles=1";

                break;

            case 'pp_updateexceptions':
                $agent_id = presspermit_REQUEST_int('agent_id');
                check_admin_referer('pp-update-exceptions_' . $agent_id, '_pp_nonce_exceptions');

                if (current_user_can('pp_assign_roles') && $pp->admin()->bulkRolesEnabled()) {
                    $this->editAgentExceptions($agent_id, $agent_type);
                }

                // cludged update of group members on role selection, due to inability to put them in the same form
                if (
                    $pp->groups()->groupTypeEditable($agent_type)
                    && $pp->groups()->userCan('pp_edit_groups', $agent_id, $agent_type)
                ) {
                    $this->triggerGroupEdit($agent_id, $agent_type);
                }

                update_user_option($current_user->ID, 'pp-permissions-tab', 'pp-add-exceptions');

                $redirect = "$url?page=presspermit-edit-permissions&agent_id=$agent_id&agent_type=$agent_type&updated=1&pp_exc=1";

                break;

            case 'creategroup':
                check_admin_referer('pp-create-group', '_wpnonce_pp-create-group');

                if (!current_user_can('pp_create_groups'))
                    wp_die(esc_html__('You are not permitted to do that.', 'press-permit-core'));

                $agent_type = presspermit_REQUEST_key('agent_type');

                if (!$agent_type = apply_filters('presspermit_query_group_type', $agent_type))
                    $agent_type = 'pp_group';

                $retval = $this->addGroup($agent_type);

                if (!is_wp_error($retval)) {
                    if (current_user_can('pp_assign_roles') && $pp->admin()->bulkRolesEnabled())
                        $this->editGroupRoles($retval, $agent_type);

                    $type_arg = ('pp_group' == $agent_type) ? '' : "&agent_type=$agent_type";
                    $redirect = "$url?page=presspermit-edit-permissions&action=edit&agent_id=$retval{$type_arg}&created=1";
                }
                break;
        } // end switch

        if (!empty($retval) && is_wp_error($retval)) {
            presspermit()->admin()->errors = $retval;
        } elseif ($redirect) {
            if ($wp_http_referer = presspermit_REQUEST_var('wp_http_referer')) {
                $arr = explode('/', esc_url_raw($wp_http_referer));
                if ($arr && !defined('PP_LEGACY_HTTP_REDIRECT')) {
                    $wp_http_referer = esc_url_raw(array_pop($arr));
                    $redirect = add_query_arg('wp_http_referer', urlencode($wp_http_referer), $redirect);
                } else {
                    $redirect = add_query_arg('wp_http_referer', urlencode(esc_url_raw($wp_http_referer)), $redirect);
                }
            }

            $redirect = esc_url_raw(add_query_arg('update', 1, $redirect));
            wp_redirect($redirect);
            exit;
        }
    }

    private function triggerGroupEdit($group_id, $agent_type, $members_only = false)
    {
        if (!in_array($agent_type, ['pp_group', 'pp_net_group'], true))
            return true;

        $group = presspermit()->groups()->getGroup($group_id, $agent_type);

        if (isset($group->metagroup_type) && in_array($group->metagroup_type, ['wp_role', 'meta_role'], true))
            $retval = true;
        elseif ($group)
            $retval = $this->editGroup($group_id, $agent_type, $members_only);
        else
            $retval = false;

        return $retval;
    }

    private function editGroupRoles($agent_id, $agent_type)
    {
        if (!current_user_can('pp_assign_roles') || !presspermit()->admin()->bulkRolesEnabled())
            return;

        if (isset($_POST['pp_add_role'])) {
            // note: group editing capability already verified at this point

            // also support bulk-assignment of user roles
            $agent_ids = (('user' == $agent_type) && !$agent_id && presspermit_REQUEST_var('member_csv'))
                ? array_map('intval', explode(',', PWP::sanitizeCSV(presspermit_REQUEST_var('member_csv'))))
                : [$agent_id];

            foreach ($agent_ids as $_agent_id) {
                if ($_agent_id) {
                    foreach (presspermit_POST_var('pp_add_role') as $add_role) { // sanitized per-element below
                        $attrib_cond = (!empty($add_role['attrib_cond'])) ? ':' . pp_permissions_sanitize_entry($add_role['attrib_cond']) : '';
                        $role = (isset($add_role['role'])) ? pp_permissions_sanitize_entry($add_role['role']) : '';

                        presspermit()->assignRoles(["{$role}{$attrib_cond}" => [$_agent_id => true]], $agent_type);
                    }
                }
            }
        }
    }

    private function editAgentExceptions($agent_id, $agent_type)
    {
        if (!current_user_can('pp_assign_roles') || !presspermit()->admin()->bulkRolesEnabled())
            return;

        if (isset($_POST['pp_add_exception'])) {
            // note: group editing capability already verified at this point
            $pp = presspermit();

            /* @todo - possible future implementation of mirroring new selections to other op(s)
            $mirror_to_ops = (!empty($_REQUEST['pp_add_exceptions_mirror_ops'])) ? $_REQUEST['pp_add_exceptions_mirror_ops'] : [];
			*/

            foreach (presspermit_POST_var('pp_add_exception') as $exc) {  // sanitized per-element below
                $exc = apply_filters('presspermit_add_exception', $exc);

                if ('(all)' == $exc['for_type']) {
                    $exc['for_type'] = '';
                }

                foreach (['mod_type', 'item_id', 'operation', 'attrib_cond', 'via_type', 'for_type', 'for_item', 'for_children'] as $var) {
                    $$var = (isset($exc[$var])) ? sanitize_key($exc[$var]) : '';
                }

                $item_id = (isset($exc['item_id'])) ? (int) $exc['item_id'] : 0;

                $args = compact('mod_type', 'item_id', 'operation');
                $args['for_item_status'] = $attrib_cond;

                if (taxonomy_exists($via_type)) {
                    $args['via_item_source'] = 'term';
                    $args['via_item_type'] = $via_type;
                    $args['item_id'] = PWP::termidToTtid($item_id, $via_type);

                } elseif ((!$via_type && post_type_exists($for_type)) || post_type_exists($via_type)) {
                    $args['via_item_source'] = 'post';
                    $args['via_item_type'] = '';

                } else {
                    $args['via_item_source'] = $via_type;
                    $args['via_item_type'] = '';
                }

                if (taxonomy_exists($for_type)) {
                    $args['for_item_source'] = 'term';
                } elseif (!$for_type || post_type_exists($for_type) || ('(all)' == $for_type)) {
                    $args['for_item_source'] = 'post';
                } else {
                    $args['for_item_source'] = $for_type;
                }

                $args['for_item_type'] = ('(all)' == $for_type) ? '' : $for_type;

                $agents = [];

                // also support bulk-assignment of user exceptions
                $agent_ids = (('user' == $agent_type) && !$agent_id && presspermit_REQUEST_var('member_csv'))
                    ? array_map('intval', explode(',', PWP::sanitizeCSV(presspermit_REQUEST_var('member_csv'))))
                    : [$agent_id];

                foreach ($agent_ids as $_agent_id) {
                    if ($_agent_id) {
                        foreach (['item' => $for_item, 'children' => $for_children] as $assign_for => $is_assigned) {
                            if ($is_assigned)
                                $agents[$assign_for][$_agent_id] = true;
                        }
                    }
                }

                $pp->assignExceptions($agents, $agent_type, $args);
            }
        }
    }

    private function addGroup($agent_type)
    {
        return $this->editGroup(0, $agent_type);
    }

    /**
     * Edit group settings based on contents of $_POST
     *
     * @param int $group_id Optional. Group ID.
     * @return int group id of the updated group
     */
    private function editGroup($group_id = 0, $agent_type = 'pp_group', $members_only = false)
    {
        global $wpdb;

        $pp = presspermit();

        if ($group_id) {
            $update = true;
            $group = $pp->groups()->getGroup($group_id, $agent_type);
        } else {
            $update = false;
            $group = (object)[];
        }

        if (!$members_only) {
            if (presspermit_is_REQUEST('group_name')) {
                $group->group_name = sanitize_text_field(presspermit_REQUEST_var('group_name'));
            }

            if (presspermit_is_REQUEST('description') && (!presspermit_is_REQUEST('action') || 'pp_updateexceptions' != presspermit_REQUEST_var('action'))) {
                $group->group_description = sanitize_text_field(presspermit_REQUEST_var('description'));
            }

            $errors = new \WP_Error();

            /* checking that username has been typed */
            if (!$group->group_name) {
                $errors->add('group_name', esc_html__('<strong>ERROR</strong>: Please enter a group name.', 'press-permit-core'));

            } elseif (!$update && !\PublishPress\Permissions\DB\GroupUpdate::groupNameAvailable($group->group_name, $agent_type)) {
                $errors->add(
                    'user_login', 
                    esc_html__('<strong>ERROR</strong>: This group name is already registered. Please choose another one.', 'press-permit-core')
                );
            }

            if ($errors->get_error_codes())
                return $errors;

            if ($update) {
                \PublishPress\Permissions\DB\GroupUpdate::updateGroup($group_id, $group, $agent_type);
            } else {
                $group_id = \PublishPress\Permissions\DB\GroupUpdate::createGroup($group, $agent_type);
            }
        }

        if ($group_id) {
            $member_types = [];

            if ($pp->groups()->userCan('pp_manage_members', $group_id, $agent_type))
                $member_types[] = 'member';

            foreach ($member_types as $member_type) {
                $any_selected = presspermit_is_REQUEST("{$member_type}_csv");

                if ($any_selected && (presspermit_REQUEST_var("{$member_type}_csv") != -1)) {
                    // handle member changes
                    $current = $pp->groups()->getGroupMembers($group_id, $agent_type, 'id', compact('member_type'));

                    $selected = ($any_selected)
                        ? explode(",", PWP::sanitizeCSV(presspermit_REQUEST_var("{$member_type}_csv")))
                        : [];

                    if (('member' != $member_type)
                        || !apply_filters('presspermit_custom_agent_update', false, $agent_type, $group_id, $selected)
                    ) {
                        if ($add_users = array_diff($selected, $current)) {
                            $pp->groups()->addGroupUser($group_id, $add_users, compact('agent_type', 'member_type'));
                        }

                        if ($remove_users = array_diff($current, $selected)) {
                            $pp->groups()->removeGroupUser($group_id, $remove_users, compact('agent_type', 'member_type'));
                        }
                    }
                }
            } // end foreach member_types

            do_action('presspermit_edited_group', $agent_type, $group_id, $update);
        }

        return $group_id;
    }
}
