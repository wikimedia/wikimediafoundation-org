<?php

namespace PublishPress\Permissions\UI;

class AgentsAjax
{
    public function __construct() 
    {
        global $wpdb, $current_blog;

        require_once(ABSPATH . '/wp-admin/includes/user.php');

        if (!presspermit_is_GET('pp_agent_search')) {
            return;
        }

        if (!$agent_type = pp_permissions_sanitize_entry(presspermit_GET_key('pp_agent_type'))) {
            return;
        }

        if (!$topic = pp_permissions_sanitize_entry(presspermit_GET_key('pp_topic'))) {
            return;
        }

        $pp = presspermit();
        $pp_admin = $pp->admin();
        $pp_groups = $pp->groups();

        $authors_clause = '';

        $orig_search_str = presspermit_GET_var('pp_agent_search');
        $search_str = sanitize_text_field($orig_search_str);
        $agent_id = presspermit_GET_int('pp_agent_id');
        $topic = str_replace(':', ',', $topic);

        $omit_admins = !presspermit_empty_GET('pp_omit_admins');
        $context = presspermit_GET_key('pp_context');
        
        if (strpos($topic, ',')) {
            $arr_topic = explode(',', $topic);
            if (isset($arr_topic[1])) {
                if (taxonomy_exists($context)) {
                    $verified = true;
                    $ops = $pp_admin->canSetExceptions(
                        $arr_topic[0],
                        $arr_topic[1],
                        ['via_item_source' => 'term', 'via_item_type' => $context, 'for_item_source' => 'post']
                    ) ? ['read' => true] : [];

                    $operations = apply_filters('presspermit_item_edit_exception_ops', $ops, 'post', $context, $arr_topic[1]);

                    if (!in_array($arr_topic[0], $operations, true)) {
                        die(-1);
                    }
                } elseif (post_type_exists($arr_topic[1])) {
                    $verified = true;
                    $ops = $pp_admin->canSetExceptions(
                        $arr_topic[0],
                        $arr_topic[1],
                        ['via_item_source' => 'post', 'for_item_source' => 'post']
                    ) ? ['read' => true] : [];

                    $operations = apply_filters('presspermit_item_edit_exception_ops', $ops, 'post', $arr_topic[1]);

                    if (empty($operations[$arr_topic[0]])) {
                        die(-1);
                    }
                }
            }
        } elseif ('member' == $topic) {
            $verified = true;
            $group_type = $pp_groups->groupTypeExists($context) ? $context : 'pp_group';
            if (!$pp_groups->userCan('pp_manage_members', $agent_id, $group_type)) {
                die(-1);
            }
        } elseif ('select-author' == $topic) {
            $verified = true;

            $post_type = (post_type_exists($context)) ? $context : 'page';

            $authors_clause = apply_filters('presspermit_select_author_clause', '', $post_type);

            $type_obj = get_post_type_object($post_type);
            if (!current_user_can($type_obj->cap->edit_others_posts)) {
                die(-1);
            }
        }

        if (empty($verified)) {
            if (!current_user_can('pp_manage_members') && !current_user_can('pp_assign_roles')) {
                die(-1);
            }
        }

        if ('user' == $agent_type) {
            if (isset($current_blog) && is_object($current_blog) && isset($current_blog->blog_id)) {
                $blog_prefix = $wpdb->get_blog_prefix($current_blog->blog_id);
            } else {
                $blog_prefix = $wpdb->get_blog_prefix();
            }

            if (
                is_multisite()
                && apply_filters('presspermit_user_search_site_only', true, compact('agent_type', 'agent_id', 'topic', 'context', 'omit_admins'))
            ) {
                $join = "INNER JOIN $wpdb->usermeta AS um ON um.user_id = $wpdb->users.ID AND um.meta_key = '{$blog_prefix}capabilities'";
            } else {
                $join = '';
            }

            $orderby = (0 === strpos($orig_search_str, ' ')) ? 'user_login' : 'user_registered DESC';

            $um_keys = (!presspermit_empty_GET('pp_usermeta_key')) ? array_map('pp_permissions_sanitize_entry', presspermit_GET_var('pp_usermeta_key')) : [];
            $um_vals = (!presspermit_empty_GET('pp_usermeta_val')) ? array_map('sanitize_text_field', presspermit_GET_var('pp_usermeta_val')) : [];

            if (defined('PP_USER_LASTNAME_SEARCH') && !defined('PP_USER_SEARCH_FIELD')) {
                $default_search_field = 'last_name';
            } elseif (defined('PP_USER_SEARCH_FIELD')) {
                $default_search_field = pp_permissions_sanitize_entry(constant('PP_USER_SEARCH_FIELD'));
            } else {
                $default_search_field = '';
            }

            if ($search_str && $default_search_field) {
                $um_keys[] = $default_search_field;
                $um_vals[] = $search_str;
                $search_str = '';
            }

            // discard duplicate selections
            $used_keys = [];
            foreach ($um_keys as $i => $keyname) {
                if (!$keyname || in_array($keyname, $used_keys, true)) {
                    unset($um_keys[$i]);
                    unset($um_vals[$i]);
                } else {
                    $used_keys[] = $keyname;
                }
            }

            $um_keys = array_values($um_keys);
            $um_vals = array_values($um_vals);

            if ($search_str) {
                $where = "WHERE (user_login LIKE '%{$search_str}%' OR user_nicename LIKE '%{$search_str}%' OR display_name LIKE '%{$search_str}%')";
            } else {
                $where = "WHERE 1=1";
            }

            if ($pp_role_search = presspermit_GET_var('pp_role_search')) {
                if ($role_filter = sanitize_text_field($pp_role_search)) {
                    $blog_prefix = $wpdb->get_blog_prefix($current_blog->blog_id);

                    $um_keys[] = "{$blog_prefix}capabilities";
                    $um_vals[] = $role_filter;
                }
            }

            // append where clause for meta value criteria
            if (!empty($um_keys)) {
                // force search values to be cast as numeric or boolean
                $force_numeric_keys = (defined('PP_USER_SEARCH_NUMERIC_FIELDS')) ? explode(',', PP_USER_SEARCH_NUMERIC_FIELDS) : [];
                $force_boolean_keys = (defined('PP_USER_SEARCH_BOOLEAN_FIELDS')) ? explode(',', PP_USER_SEARCH_BOOLEAN_FIELDS) : [];

                for ($i = 0; $i < count($um_keys); $i++) {
                    $join .= " INNER JOIN $wpdb->usermeta AS um_{$um_keys[$i]} ON um_{$um_keys[$i]}.user_id = $wpdb->users.ID"
                        . " AND um_{$um_keys[$i]}.meta_key = '{$um_keys[$i]}'";

                    $val = trim($um_vals[$i]);

                    if (in_array($um_keys[$i], $force_numeric_keys)) {
                        if (in_array($val, ['true', 'false', 'yes', 'no'])) {
                            $val = (bool)$val;
                        }

                        $val = (int)$val;
                    } elseif (in_array($val, $force_boolean_keys)) {
                        $val = strval((bool)$val);
                    }

                    if ($val) {
                        $where .= " AND um_{$um_keys[$i]}.meta_value LIKE '%{$val}%'";
                    } else {
                        $where .= " AND um_{$um_keys[$i]}.meta_value = '{$val}'";
                    }
                }
            }

            $limit = (defined('PP_USER_SEARCH_LIMIT')) ? abs(intval(constant('PP_USER_SEARCH_LIMIT'))) : 0;
            if ($limit) {
                $limit_clause = "LIMIT $limit";
            } else {
                $limit_clause = (defined('PP_USER_SEARCH_UNLIMITED')) ? '' : 'LIMIT 10000';
            }

            $query = "SELECT ID, user_login, display_name FROM $wpdb->users $join $where $authors_clause ORDER BY $orderby $limit_clause";

            $results = $wpdb->get_results($query);

            if ($results) {
                $omit_users = [];

                // determine all current users for group in question
                if (!empty($agent_id)) {
                    $topic = isset($topic) ? $topic : '';
                    $group_type = ($context && $pp_groups->groupTypeExists($context)) ? $context : 'pp_group';
                    $omit_users = $pp_groups->getGroupMembers($agent_id, $group_type, 'id', ['member_type' => $topic, 'status' => 'any']);
                } elseif ($omit_admins) {
                    if ($admin_roles = $pp_admin->getAdministratorRoles()) {  // Administrators can't be excluded; no need to include or enable them
                        $role_csv = implode("','", array_map('sanitize_key', array_keys($admin_roles)));
                        $omit_users = $wpdb->get_col(
                            "SELECT u.ID FROM $wpdb->users AS u INNER JOIN $wpdb->pp_group_members AS gm ON u.ID = gm.user_id"
                            . " INNER JOIN $wpdb->pp_groups AS g ON gm.group_id = g.ID"
                            . " WHERE g.metagroup_type = 'wp_role' AND g.metagroup_id IN ('$role_csv')"
                        );
                    }
                }

                foreach ($results as $row) {
                    if (!in_array($row->ID, $omit_users)) {
                        if (defined('PP_USER_RESULTS_DISPLAY_NAME')) {
                            $title = ($row->user_login != $row->display_name) ? $row->user_login : '';
                            echo "<option value='" . esc_attr($row->ID) . "' class='pp-new-selection' title='" . esc_attr($title) . "'>" . esc_html($row->display_name) . "</option>";
                        } else {
                            $title = ($row->user_login != $row->display_name) ? $row->display_name : '';
                            echo "<option value='" . esc_attr($row->ID) . "' class='pp-new-selection title='" . esc_attr($title) . "'>" . esc_html($row->user_login) . "</option>";
                        }
                    }
                }
            }
        } else {
            $reqd_caps = apply_filters('presspermit_edit_groups_reqd_caps', ['pp_edit_groups']);

            // determine all currently stored groups (of any status) for user in question (not necessarily logged user)
            if (!empty($agent_id)) {
                $omit_groups = $pp_groups->getGroupsForUser($agent_id, $agent_type, ['status' => 'any']);
            } else {
                $omit_groups = [];
            }

            if ($groups = $pp_groups->getGroups(
                $agent_type,
                ['filtering' => true, 'include_norole_groups' => false, 'reqd_caps' => $reqd_caps, 'search' => $search_str]
            )) {
                foreach ($groups as $row) {
                    if ((empty($row->metagroup_id) || is_null($row->metagroup_id)) && !isset($omit_groups[$row->ID])) {
                        echo "<option value='" . esc_attr($row->ID) . "'>". esc_html($row->name) . "</option>";
                    }
                }
            }
        }
    }
}
