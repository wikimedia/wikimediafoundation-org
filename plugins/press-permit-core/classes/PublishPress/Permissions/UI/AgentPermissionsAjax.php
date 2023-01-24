<?php

namespace PublishPress\Permissions\UI;

class AgentPermissionsAjax
{
    public function __construct() {
        $pp = presspermit();
        $pp_admin = $pp->admin();

        if (!$action = presspermit_GET_key('pp_ajax_agent_permissions')) {
            exit;
        }

        if (!$pp_admin->bulkRolesEnabled()) {
            exit;
        }

        global $wpdb;

        $html = '';

        $agent_type = presspermit_GET_key('agent_type');
        $agent_id = presspermit_GET_int('agent_id');

        // safeguard prevents accidental modification of roles for other groups / users
        if ($agent_type && $agent_id) {
            $agent_clause = $wpdb->prepare(
                "agent_type = %s AND agent_id = %d AND",
                $agent_type,
                $agent_id
            );
        } else {
            $agent_clause = '';
        }

        switch ($action) {
            case 'roles_remove':
                global $current_user;

                if (!$pp_ass_ids = presspermit_GET_var('pp_ass_ids')) {
                    exit;
                }

                if (!current_user_can('pp_assign_roles') || !$pp_admin->bulkRolesEnabled()) {
                    exit;
                }

                $deleted_ass_ids = [];

                $input_vals = explode('|', PWP::sanitizeCSV(sanitize_text_field($pp_ass_ids)));
                foreach ($input_vals as $id_csv) {
                    $ass_ids = $this->editableAssignmentIDs(explode(',', $id_csv));
                    $deleted_ass_ids = array_merge($deleted_ass_ids, $ass_ids);
                }

                if ($deleted_ass_ids) {
                    require_once(PRESSPERMIT_CLASSPATH . '/DB/PermissionsUpdate.php');

                    $id_csv = implode("','", array_map('intval', $deleted_ass_ids));

                    $results = $wpdb->get_results(
                        "SELECT agent_type, agent_id, role_name FROM $wpdb->ppc_roles"
                        . " WHERE $agent_clause assignment_id IN ('$id_csv')"
                    );

                    foreach ($results as $row) {
                        if ($agent_clause) {
                            $this_group_clase = $agent_clause;
                        } else {
                            $this_group_clase = $wpdb->prepare(
                                "agent_type = %s AND agent_id = %d AND",
                                $row->agent_type,
                                $row->agent_id
                            );
                        }

                        if ($_ass_ids = $wpdb->get_col(
                                $wpdb->prepare(
                                    "SELECT assignment_id FROM $wpdb->ppc_roles WHERE $this_group_clase role_name=%s",
                                    $row->role_name
                                )
                            )
                        ) {
                            \PublishPress\Permissions\DB\PermissionsUpdate::removeRolesById($_ass_ids);
                        }
                    }

                    do_action('presspermit_supplemental_roles_deleted', $deleted_ass_ids, $agent_type, $agent_id);
                    do_action('presspermit_edited_group', $agent_type, $agent_id, true);
                }

                echo '<!--ppResponse-->' . esc_html(implode('|', $input_vals)) . '<--ppResponse-->';
                break;

            case 'exceptions_remove':
                if (!$pp_eitem_ids = presspermit_GET_var('pp_eitem_ids')) {
                    exit;
                }

                if (!current_user_can('pp_assign_roles') || !$pp_admin->bulkRolesEnabled()) {
                    exit;
                }

                $deleted_eitem_ids = [];

                $input_vals = explode('|', PWP::sanitizeCSV(sanitize_text_field($pp_eitem_ids)));
                foreach ($input_vals as $id_csv) {
                    $eitem_ids = $this->editableEitemIDs(explode(',', $id_csv));
                    $deleted_eitem_ids = array_merge($deleted_eitem_ids, $eitem_ids);

                    // possible TODO: remove elem from $input_vals if content-specific assign_roles authentication fails
                }

                if ($deleted_eitem_ids) {
                    require_once(PRESSPERMIT_CLASSPATH . '/DB/PermissionsUpdate.php');

                    $exc_clause = ($agent_clause) ? "exception_id IN ( SELECT exception_id FROM $wpdb->ppc_exceptions WHERE $agent_clause 1=1 ) AND" : '';

                    $eitem_id_csv = implode("','", array_map('intval', $deleted_eitem_ids));

                    // safeguard against accidental deletion of a different agent's exceptions
                    $results = $wpdb->get_results(
                        "SELECT exception_id, item_id FROM $wpdb->ppc_exception_items"
                        . " WHERE $exc_clause eitem_id IN ('$eitem_id_csv')"
                    );

                    foreach ($results as $row) {
                        // also delete any redundant item exceptions for this agent
                        if ($_eitem_ids = $wpdb->get_col(
                            $wpdb->prepare(
                                "SELECT eitem_id FROM $wpdb->ppc_exception_items WHERE exception_id=%d AND item_id=%d",
                                $row->exception_id,
                                $row->item_id
                            )
                        )) {
                            \PublishPress\Permissions\DB\PermissionsUpdate::removeExceptionItemsById($_eitem_ids);
                        }
                    }

                    do_action('presspermit_exception_items_deleted', $deleted_eitem_ids, $agent_type, $agent_id);
                    do_action('presspermit_edited_group', $agent_type, $agent_id, true);
                }

                echo '<!--ppResponse-->' . esc_html(implode('|', $input_vals)) . '<--ppResponse-->';
                break;

            case 'exceptions_propagate':
            case 'exceptions_unpropagate':
            case 'exceptions_children_only':
                if (!$pp_eitem_ids = presspermit_GET_var('pp_eitem_ids')) {
                    exit;
                }

                if (!current_user_can('pp_assign_roles')) {
                    exit;
                }

                $edited_input_ids = [];
                $all_eitem_ids = [];

                $input_vals = explode('|', PWP::sanitizeCSV(sanitize_text_field($pp_eitem_ids)));

                foreach ($input_vals as $id_csv) {
                    $eitem_ids = $this->editableEitemIDs(explode(',', $id_csv));

                    if ($agent_type && $agent_id) {
                        $agent_clause = "e.agent_type = '$agent_type' AND e.agent_id = '$agent_id' AND";
                    } else {
                        $agent_clause = '';
                    }

                    $eitem_id_csv = implode("','", array_map('intval', $eitem_ids));

                    if ($row = $wpdb->get_row(
                        "SELECT * FROM $wpdb->ppc_exception_items AS i"
                        . " INNER JOIN $wpdb->ppc_exceptions AS e ON i.exception_id = e.exception_id"
                        . " WHERE $agent_clause eitem_id IN ('$eitem_id_csv') LIMIT 1"
                    )) {
                        $args = (array)$row;

                        if ('exceptions_propagate' == $action) {
                            $agents = ['children' => [$agent_id => true]];
                            $pp->assignExceptions($agents, $agent_type, $args);
                        } elseif ('exceptions_unpropagate' == $action) {
                            $agents = ['item' => [$agent_id => true]];
                            $pp->assignExceptions($agents, $agent_type, $args);

                            $wpdb->delete($wpdb->ppc_exception_items, ['assign_for' => 'children', 'exception_id' => $row->exception_id, 'item_id' => $row->item_id]);

                            $wpdb->delete($wpdb->ppc_exception_items, ['inherited_from' => $row->eitem_id]);
                        } elseif ('exceptions_children_only' == $action) {
                            $agents = ['children' => [$agent_id => true]];
                            $pp->assignExceptions($agents, $agent_type, $args);

                            $wpdb->delete($wpdb->ppc_exception_items, ['assign_for' => 'item', 'exception_id' => $row->exception_id, 'item_id' => $row->item_id]);
                        }

                        $edited_input_ids[] = $id_csv;
                    }

                    $all_eitem_ids = array_merge($all_eitem_ids, $eitem_ids);
                }

                do_action('presspermit_exception_items_updated', $all_eitem_ids, $agent_type, $agent_id);
                do_action('presspermit_edited_group', $agent_type, $agent_id, true);

                echo '<!--ppResponse-->' . esc_html(presspermit_GET_key('pp_ajax_agent_permissions')) . '~' . esc_html(implode('|', $edited_input_ids)) . '<--ppResponse-->';
                break;

            default:
                // mirror specified existing exception items to specified operation
                if (0 === strpos($action, 'exceptions_mirror_')) {
                    if (!$pp_eitem_ids = presspermit_GET_var('pp_eitem_ids')) {
                        exit;
                    }

                    $arr = explode('_', $action);

                    if (count($arr) < 3) {
                        break;
                    }

                    $mirror_op = $arr[2];

                    if (!$op_obj = $pp_admin->getOperationObject($mirror_op)) {
                        break;
                    }

                    $edited_input_ids = [];
                    $all_eitem_ids = [];

                    $input_vals = explode('|', PWP::sanitizeCSV(sanitize_text_field($pp_eitem_ids)));

                    foreach ($input_vals as $id_csv) {
                        $eitem_ids = $this->editableEitemIDs(explode(',', $id_csv));

                        if ($agent_type && $agent_id) {
                            $agent_clause = "e.agent_type = '$agent_type' AND e.agent_id = '$agent_id' AND";
                        } else {
                            $agent_clause = '';
                        }

                        $eitem_id_csv = implode("','", array_map('intval', $eitem_ids));

                        foreach ($results = $wpdb->get_results(
                            "SELECT * FROM $wpdb->ppc_exception_items AS i"
                            . " INNER JOIN $wpdb->ppc_exceptions AS e ON i.exception_id = e.exception_id"
                            . " WHERE $agent_clause eitem_id IN ('$eitem_id_csv')"
                        ) as $row) {
                            $args = (array)$row;
                            $args['operation'] = $mirror_op;
                            $agents = [$row->assign_for => [$agent_id => true]];
                            $pp->assignExceptions($agents, $agent_type, $args);

                            $edited_input_ids[] = $id_csv;
                        }

                        $all_eitem_ids = [];
                    }

                    do_action('presspermit_exception_items_mirrored', $all_eitem_ids, $agent_type, $agent_id);

                    echo '<!--ppResponse-->' . 'exceptions_mirror' . '~' . esc_attr(implode('|', $edited_input_ids)) . '<--ppResponse-->';
                    break;
                }

        } // end switch
    }

    private function getRoleAttributes($role_name)
    {
        $arr_name = explode(':', $role_name);

        $return['base_role_name'] = $arr_name[0];
        $return['source_name'] = (!empty($arr_name[1])) ? $arr_name[1] : '';
        $return['object_type'] = (!empty($arr_name[2])) ? $arr_name[2] : '';
        $return['attribute'] = (!empty($arr_name[3])) ? $arr_name[3] : '';
        $return['condition'] = (!empty($arr_name[4])) ? $arr_name[4] : '';

        return (object)$return;
    }

    private function editableAssignmentIDs($ass_ids)
    {
        if (presspermit()->isUserAdministrator()) {
            return $ass_ids;
        }

        global $wpdb;
        $add_ids_csv = implode("','", array_map('intval', $ass_ids));
        $results = $wpdb->get_results("SELECT assignment_id, role_name FROM $wpdb->ppc_roles WHERE assignment_id IN ('$ass_ids_csv')");

        $remove_ids = [];

        foreach ($results as $row) {
            if (!$role_attrib = $this->getRoleAttributes($row->role_name)) {
                continue;
            }

            if (!$pp_admin->userCanAdminRole($role_attrib->base_role_name, $role_attrib->object_type)) {
                $remove_ids[] = $row->assignment_id;
            }
        }

        $ass_ids = array_diff($ass_ids, $remove_ids);
        return $ass_ids;
    }

    private function editableEitemIDs($ass_ids)
    {
        // governed universally by pp_admin->bulkRolesEnabled()
        return $ass_ids;
    }
}
