<?php
namespace PublishPress\Permissions\Collab;

class RoleAdmin
{
    public static function hasGroupCap($has_cap, $cap_name, $group_id, $group_type)
    {
        static $editable_group_types;
        $user = presspermit()->getUser();

        if (!isset($manageable_groups))
            $editable_group_types = apply_filters('presspermit_editable_group_types', ['pp_group']);

        if (false === $group_id) {
            if (!$has_cap) {  // todo: deal with exclude/include to avoid empty listing?
                foreach ($editable_group_types as $_group_type) {
                    if ($group_type && ($_group_type != $group_type))
                        continue;

                    if (!isset($user->except["manage_{$_group_type}"]))
                        $user->retrieveExceptions(['manage'], [$_group_type]);

                    if (!empty($user->except["manage_{$_group_type}"][$_group_type]['']['additional'][$_group_type]['']))
                        return true;
                }
            }
        } else {
            if (!in_array($group_type, $editable_group_types, true))
                return false;

            // temp workaround
            if ('pp_net_group' == $group_type)
                $group_type = 'pp_group';

            if (!isset($user->except["manage_{$group_type}"]))
                $user->retrieveExceptions(['manage'], [$group_type]);

            if ($has_cap) {
                if (!empty($user->except["manage_{$group_type}"]['pp_group']['']['include']['pp_group'][''])) {
                    if (!in_array($group_id, $user->except["manage_{$group_type}"]['pp_group']['']['include']['pp_group']['']))
                        return false;

                } elseif (!empty($user->except["manage_{$group_type}"]['pp_group']['']['exclude']['pp_group'][''])) {
                    if (in_array($group_id, $user->except["manage_{$group_type}"]['pp_group']['']['exclude']['pp_group']['']))
                        return false;
                }
            } else {
                if (!empty($user->except["manage_{$group_type}"]['pp_group']['']['additional']['pp_group']['']))
                    return in_array($group_id, $user->except["manage_{$group_type}"]['pp_group']['']['additional']['pp_group']['']);
            }
        }

        return $has_cap;
    }

    public static function retrieveAdminGroups($editable_group_ids, $operation)
    {
        $user = presspermit()->getUser();

        $user->retrieveExceptions('manage');

        if (!is_array($editable_group_ids))
            $editable_group_ids = [];

        // group management exceptions permit full group editing (as well as member management)
        if (!empty($user->except['manage_pp_group']['pp_group']['']['additional']['pp_group']['']))
            $editable_group_ids = array_merge(
                $editable_group_ids, 
                $user->except['manage_pp_group']['pp_group']['']['additional']['pp_group']['']
            );

        if (('manage' == $operation) && current_user_can('pp_manage_own_groups')) {
            if (!$agent_type = apply_filters('presspermit_query_group_type', $agent_type))
                $agent_type = 'pp_group';

            // work around loading of pp_net_group membership into $user->groups['pp_group'] on bulk member management post
            if (('pp_net_group' == $agent_type) && !isset($user->groups[$agent_type]))
                $agent_type = 'pp_group';

            if (current_user_can('pp_manage_own_groups'))
                $editable_group_ids = array_merge($editable_group_ids, array_keys($user->groups[$agent_type]));
        }

        return $editable_group_ids;
    }

    public static function canSetExceptions($can, $operation, $for_item_type, $args = [])
    {
        $defaults = [
            'item_id' => 0, 
            'via_item_source' => 'post', 
            'via_item_type' => '', 
            'for_item_source' => 'post', 
            'is_administrator' => false
        ];

        $args = array_merge($defaults, $args);
        foreach (array_keys($defaults) as $var) {
            $$var = $args[$var];
        }

        if (!$is_administrator) {
            $user = presspermit()->getUser();

            $can_assign_edit_exceptions = false;

            if (presspermit()->getOption('non_admins_set_edit_exceptions') 
            && defined('PP_NON_EDITORS_SET_EDIT_EXCEPTIONS') && PP_NON_EDITORS_SET_EDIT_EXCEPTIONS
            ) {
                $can_edit_published = true;
            } else {
                $can_edit_published = false;

                if ($type_obj = get_post_type_object($for_item_type)) {
                    $can_edit_published = !empty($user->allcaps[$type_obj->cap->edit_published_posts]);

                    if ($can_edit_published) {
                        // also require edit_others_posts (unless this is a post-assigned exception for user's own post)
                        if (!$can_edit_published = !empty($user->allcaps[$type_obj->cap->edit_others_posts])) {
                            if ('post' == $via_item_source) {
                                if (!$item_id)
                                    $item_id = PWP::getPostID();

                                $_post = ($item_id) ? get_post($item_id) : false;
                                $can_edit_published = $_post && ($_post->post_author == $user->ID);
                            }
                        }
                    }
                }
            }

            $can_assign_edit_exceptions = $can_edit_published && current_user_can('pp_set_edit_exceptions');
        }

        switch ($operation) {
            case 'edit' :
            case 'publish' :
                $can = $is_administrator || $can_assign_edit_exceptions;
                break;

            case 'fork':
                $can = class_exists('Fork', false) && !defined('PP_DISABLE_FORKING_SUPPORT') 
                && ($is_administrator || $can_assign_edit_exceptions || ($can_edit_published && current_user_can('pp_set_fork_exceptions')));
                
                break;

            case 'copy':
                $can = defined('PUBLISHPRESS_REVISIONS_VERSION') && ($is_administrator || $can_assign_edit_exceptions || (
                    $can_edit_published && current_user_can('pp_set_copy_exceptions'))
                );
                break;

            case 'revise':
                $can = (defined('PUBLISHPRESS_REVISIONS_VERSION') || defined('REVISIONARY_VERSION')) && ($is_administrator || $can_assign_edit_exceptions || (
                    $can_edit_published && current_user_can('pp_set_revise_exceptions'))
                );
                break;

            case 'associate':
                if ('term' == $via_item_source) {
                    $can = is_taxonomy_hierarchical($for_item_type) 
                    && ($is_administrator || current_user_can('pp_set_term_associate_exceptions'));
                } else {
                    $can = is_post_type_hierarchical($for_item_type) && ($is_administrator 
                    || ($can_edit_published && current_user_can('pp_set_associate_exceptions')));
                }
                break;

            case 'assign':
                $can = $is_administrator || ($can_edit_published && current_user_can('pp_set_term_assign_exceptions'));
                break;

            case 'manage':
                $can = $is_administrator || current_user_can('pp_set_term_manage_exceptions');
                break;
        }

        return $can;
    }
}
