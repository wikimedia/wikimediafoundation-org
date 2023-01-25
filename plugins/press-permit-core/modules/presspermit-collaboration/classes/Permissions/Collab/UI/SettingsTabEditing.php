<?php
namespace PublishPress\Permissions\Collab\UI;

use \PublishPress\Permissions\UI\SettingsAdmin as SettingsAdmin;

class SettingsTabEditing
{
    function __construct()
    {
        add_filter('presspermit_option_tabs', [$this, 'optionTabs'], 4);
        add_filter('presspermit_section_captions', [$this, 'sectionCaptions']);
        add_filter('presspermit_option_captions', [$this, 'optionCaptions']);
        add_filter('presspermit_option_sections', [$this, 'optionSections'], 15);

        add_action('presspermit_editing_options_pre_ui', [$this, 'optionsPreUI']);
        add_action('presspermit_editing_options_ui', [$this, 'optionsUI']);
        add_action('presspermit_options_ui_insertion', [$this, 'advanced_tab_permissions_options_ui'], 5, 2); // hook for UI insertion on Settings > Advanced tab

        add_filter('presspermit_cap_descriptions', [$this, 'flt_cap_descriptions'], 3);  // priority 3 for ordering before PPS and PPCC additions in caps list
    }

    function optionTabs($tabs)
    {
        $tabs['editing'] = esc_html__('Editing', 'press-permit-core');
        return $tabs;
    }

    function sectionCaptions($sections)
    {
        $new = [
            'post_editor' => esc_html__('Editor Options', 'press-permit-core'),
            'content_management' => esc_html__('Posts / Pages Listing', 'press-permit-core'),
            'page_structure' => esc_html__('Page Structure', 'press-permit-core'),
            'limited_editing_elements' => esc_html__('Limited Editing Elements', 'press-permit-core'),
            'media_library' => esc_html__('Media Library', 'press-permit-core'),
            'nav_menu_management' => esc_html__('Nav Menu Editing', 'press-permit-core'),
            'user_management' => esc_html__('User Management', 'press-permit-core'),
            'post_forking' => esc_html__('Post Forking', 'press-permit-core'),
        ];


        $key = 'editing';
        $sections[$key] = (isset($sections[$key])) ? array_merge($sections[$key], $new) : $new;
        return $sections;
    }

    function optionCaptions($captions)
    {
        $opt = [
            'lock_top_pages' => esc_html__('Pages can be set or removed from Top Level by:', 'press-permit-core'),
            'editor_hide_html_ids' => esc_html__('Limited Editing Elements', 'press-permit-core'),
            'editor_ids_sitewide_requirement' => esc_html__('Specified element IDs also require the following site-wide Role:', 'press-permit-core'),
            'admin_others_attached_files' => esc_html__("List other users' uploads if attached to an editable post", 'press-permit-core'),
            'admin_others_attached_to_readable' => esc_html__("List other users' uploads if attached to a readable post", 'press-permit-core'),
            'admin_others_unattached_files' => esc_html__("Other users' unattached uploads listed by default", 'press-permit-core'),
            'edit_others_attached_files' => esc_html__("Edit other users' uploads if attached to an editable post", 'press-permit-core'),
            'own_attachments_always_editable' => esc_html__('Users can always edit their own attachments', 'press-permit-core'),
            'admin_nav_menu_filter_items' => esc_html__('List only user-editable content as available items', 'press-permit-core'),
            'admin_nav_menu_partial_editing' => esc_html__('Allow Renaming of Uneditable Items', 'press-permit-core'),
            'admin_nav_menu_lock_custom' => esc_html__('Lock custom menu items', 'press-permit-core'),
            'limit_user_edit_by_level' => esc_html__('Limit User Edit by Level', 'press-permit-core'),
            'default_privacy' => esc_html__('Default visibility for new posts:', 'press-permit-core'),
            'list_others_uneditable_posts' => esc_html__('List other user\'s uneditable posts', 'press-permit-core'),
            'page_parent_order' => esc_html__('Order Page Parent dropdown by Title', 'press-permit-core'),
            'add_author_pages' => esc_html__('Bulk-Add Author Pages (on Users screen)', 'press-permit-core'),
            'publish_author_pages' => esc_html__('Publish Author Pages at bulk creation', 'press-permit-core'),
            'fork_published_only' => esc_html__('Fork published posts only', 'press-permit-core'),
            'fork_require_edit_others' => esc_html__('Forking enforces edit_others_posts capability', 'press-permit-core'),
            'force_taxonomy_cols' => esc_html__('Add taxonomy columns to Edit Posts screen', 'press-permit-core'),
            'non_admins_set_edit_exceptions' => esc_html__('Non-Administrators can set Editing Permissions for their editable posts', 'press-permit-core'),
            'publish_exceptions' => esc_html__('Assign Publish Permissions separate from Edit Permissions', 'press-permit-core'),
        ];

        return array_merge($captions, $opt);
    }

    function optionSections($sections)
    {
        // Editing tab
        $new = [
            'page_structure' => ['lock_top_pages'],
            'user_management' => ['limit_user_edit_by_level', 'add_author_pages', 'publish_author_pages'],
            'post_editor' => ['default_privacy', 'force_default_privacy', 'page_parent_order'],
            'content_management' => ['list_others_uneditable_posts', 'force_taxonomy_cols'],
            'media_library' => ['admin_others_attached_files', 'admin_others_attached_to_readable', 'admin_others_unattached_files', 'edit_others_attached_files', 'own_attachments_always_editable'],
            'nav_menu_management' => ['admin_nav_menu_filter_items', 'admin_nav_menu_partial_editing', 'admin_nav_menu_lock_custom'],
            'post_forking' => ['fork_published_only', 'fork_require_edit_others'],
        ];

        if (!PWP::isBlockEditorActive()) {
            if (presspermit()->getOption('advanced_options'))
                $new['limited_editing_elements'] = ['editor_hide_html_ids', 'editor_ids_sitewide_requirement'];
        }

        $tab = 'editing';
        $sections[$tab] = (isset($sections[$tab])) ? array_merge($sections[$tab], $new) : $new;

        // Advanced tab
        $new = ['permissions_admin' => ['non_admins_set_edit_exceptions', 'publish_exceptions']];

        $tab = 'advanced';

        foreach (array_keys($new) as $section) {
            $sections[$tab][$section] = (isset($sections[$tab][$section])) ? array_merge($sections[$tab][$section], $new[$section]) : $new[$section];
        }

        return $sections;
    }

    function optionsPreUI()
    {
        if (false && presspermit()->getOption('display_hints')) :
            ?>
            <div class="pp-optionhint">
                <?php
                SettingsAdmin::echoStr('collaborative-publishing');
                ?>
            </div>
        <?php
        endif;
    }

    function optionsUI()
    {
        $pp = presspermit();

        $ui = \PublishPress\Permissions\UI\SettingsAdmin::instance(); 
        $tab = 'editing';

        $section = 'post_editor';                        // --- EDITOR OPTIONS SECTION ---
        if (!empty($ui->form_options[$tab][$section])) :
            ?>
            <tr>
                <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                <td>

                    <span><?php echo esc_html($ui->option_captions['default_privacy']); ?></span>
                    <br/>

                    <div class="agp-vspaced_input default_privacy" style="margin-left: 2em;">
                        <?php
                        $option_name = 'default_privacy';
                        $ui->all_otype_options[] = $option_name;

                        $opt_values = array_merge(array_fill_keys($pp->getEnabledPostTypes(), 0), $ui->getOptionArray($option_name));  // add enabled types whose settings have never been stored
                        $opt_values = array_intersect_key($opt_values, array_fill_keys($pp->getEnabledPostTypes(), 0));  // skip stored types that are not enabled
                        $opt_values = array_diff_key($opt_values, array_fill_keys(apply_filters('presspermit_disabled_default_privacy_types', ['forum', 'topic', 'reply']), true));

                        // todo: force default status in Gutenberg
                        if (defined('PRESSPERMIT_STATUSES_VERSION')) {
                            $do_force_option = true;
                            $ui->all_otype_options[] = 'force_default_privacy';
                            $force_values = array_merge(array_fill_keys($pp->getEnabledPostTypes(), 0), $ui->getOptionArray('force_default_privacy'));  // add enabled types whose settings have never been stored
                        } else
                            $do_force_option = false;
                        ?>
                        <table class='agp-vtight_input agp-rlabel'>
                            <?php

                            foreach ($opt_values as $object_type => $setting) :
                                if ('attachment' == $object_type) continue;

                                $id = $option_name . '-' . $object_type;
                                $name = "{$option_name}[$object_type]";
                                ?>
                                <tr>
                                    <td class="rlabel">
                                        <input name='<?php echo esc_attr($name); ?>' type='hidden' value=''/>
                                        <label for='<?php echo esc_attr($id); ?>'><?php if ($type_obj = get_post_type_object($object_type)) echo esc_html($type_obj->labels->name); else echo esc_html($object_type); ?></label>
                                    </td>

                                    <td><select name='<?php echo esc_attr($name); ?>' id='<?php echo esc_attr($id); ?>' autocomplete='off'>
                                            <option value=''><?php esc_html_e('Public'); ?></option>
                                            <?php foreach (get_post_stati(['private' => true], 'object') as $status_obj) :
                                                $selected = ($setting === $status_obj->name) ? ' selected ' : '';
                                                ?>
                                                <option value='<?php echo esc_attr($status_obj->name); ?>' <?php echo esc_attr($selected); ?>><?php echo esc_html($status_obj->label); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        if ($do_force_option) :
                                            $id = 'force_default_privacy-' . $object_type;
                                            $name = "force_default_privacy[$object_type]";
                                            $style = ($setting) ? '' : 'display:none';
                                            $checked = (!empty($force_values[$object_type]) || PWP::isBlockEditorActive($object_type)) ? ' checked ' : '';
                                            $disabled = (PWP::isBlockEditorActive($object_type)) ? " disabled " : '';
                                            ?>
                                            <input name='<?php echo esc_attr($name); ?>' type='hidden' value='0'/>
                                            &nbsp;<label style='<?php echo esc_attr($style);?>' for="<?php echo esc_attr($id);?>"><input
                                                    type="checkbox" <?php echo esc_attr($checked);?><?php echo esc_attr($disabled);?>id="<?php echo esc_attr($id);?>"
                                                    name="<?php echo esc_attr($name); ?>"
                                                    value="1"/><?php if ($do_force_option) : ?>&nbsp;<?php esc_html_e('lock', 'press-permit-core'); ?><?php endif; ?>
                                        </label>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                            ?>
                        </table>
                    </div>

                    <script type="text/javascript">
                        /* <![CDATA[ */
                        jQuery(document).ready(function ($) {
                            $('div.default_privacy select').on('click', function() {
                                $(this).parent().find('label').toggle($(this).val() != '');
                            });

                            $('#add_author_pages').on('click', function() {
                                $('div.publish_author_pages').toggle($(this).is(':checked'));
                            });
                        });
                        /* ]]> */
                    </script>

                    <br/>
                    <?php
                    $ui->optionCheckbox('page_parent_order', $tab, $section);
                    ?>
                </td>
            </tr>
        <?php endif; // any options accessable in this section

        $section = 'content_management';                        // --- POSTS / PAGES LISTING SECTION ---
        if (!empty($ui->form_options[$tab][$section])) :
            ?>
            <tr>
                <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                <td>
                    <?php
                    if (!defined('PP_ADMIN_READONLY_LISTABLE') || ($pp->getOption('admin_hide_uneditable_posts') && !defined('PP_ADMIN_POSTS_NO_FILTER'))) {
                        $ui->optionCheckbox('list_others_uneditable_posts', $tab, $section, true);
                    }

                    $ui->optionCheckbox('force_taxonomy_cols', $tab, $section, true, '');
                    ?>
                </td>
            </tr>
        <?php endif; // any options accessable in this section


        $section = 'page_structure';                                    // --- PAGE STRUCTURE SECTION ---
        if (!empty($ui->form_options[$tab][$section])) : ?>
            <tr>
                <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                <td>
                    <?php
                    $id = 'lock_top_pages';
                    $ui->all_options[] = $id;
                    $current_setting = strval($ui->getOption($id));  // force setting and corresponding keys to string, to avoid quirks with integer keys

                    echo esc_html($ui->option_captions['lock_top_pages']);

                    $captions = ['no_parent_filter' => esc_html__('no Page Parent filter', 'press-permit-core'), 'author' => esc_html__('Page Authors, Editors and Administrators', 'press-permit-core'), '' => esc_html__('Page Editors and Administrators', 'press-permit-core'), '1' => esc_html__('Administrators', 'press-permit-core')];

                    foreach ($captions as $key => $value) {
                        $key = strval($key);
                        echo "<div style='margin: 0 0 0.5em 2em;'><label for='" . esc_attr("{$id}_{$key}") . "'>";
                        $checked = ($current_setting === $key) ? ' checked ' : '';

                        echo "<input name='" . esc_attr($id) . "' type='radio' id='" . esc_attr("{$id}_{$key}") . "' value='" . esc_attr($key) . "' " . esc_attr($checked) . " /> ";
                        echo esc_html($value);
                        echo '</label></div>';
                    }

                    echo '<span class="pp-subtext">';
                    if ($ui->display_hints) {
                        SettingsAdmin::echoStr('lock_top_pages');
                    }

                    echo '</span>';
                    ?>

                </td>
            </tr>
        <?php endif; // any options accessable in this section

        if (!PWP::isBlockEditorActive()) {
            $section = 'limited_editing_elements';                            // --- LIMITED EDITING ELEMENTS SECTION ---
            if (!empty($ui->form_options[$tab][$section])) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                    <td>
                        <?php if (in_array('editor_hide_html_ids', $ui->form_options[$tab][$section], true)) : ?>
                            <?php
                            $option_name = 'editor_hide_html_ids';
                            $ui->all_options[] = $option_name;

                            $opt_val = $ui->getOption($option_name);

                            // note: 'post:post' otype option is used for all non-page types

                            echo '<div class="agp-vspaced_input">';
                            echo '<span class="pp-vtight">';
                            esc_html_e('Edit Form HTML IDs:', 'press-permit-core');
                            ?>
                            <label for="<?php echo esc_attr($option_name); ?>">
                                <input name="<?php echo esc_attr($option_name); ?>" type="text" size="45" style="width: 95%"
                                       id="<?php echo esc_attr($option_name); ?>" value="<?php echo esc_attr($opt_val); ?>"/>
                            </label>
                            </span>
                            <br/>
                            <?php
                            printf(
                                esc_html__('%1$s sample IDs:%2$s %3$s', 'press-permit-core'), 
                                "<a href='javascript:void(0)' onclick='jQuery(document).ready(function($){ $('#pp_sample_ids').show(); });'>", 
                                '</a>', 
                            
                                '<span id="pp_sample_ids" class="pp-gray" style="display:none">' 
                                . 'password-span, slugdiv, edit-slug-box, authordiv, commentstatusdiv, trackbacksdiv, postcustom, revisionsdiv, pageparentdiv' 
                                . '</span>'
                            );
                            ?>
                            </div>
                            
                            <?php
                            if ($ui->display_hints) {
                                echo '<div class="pp-subtext">';
                                SettingsAdmin::echoStr('limited_editing_elements');
                                echo '</div>';
                            }
                            ?>
                            <br/>
                        <?php endif; ?>

                        <?php if (in_array('editor_ids_sitewide_requirement', $ui->form_options[$tab][$section], true)) :
                            $id = 'editor_ids_sitewide_requirement';
                            $ui->all_options[] = $id;

                            // force setting and corresponding keys to string, to avoid quirks with integer keys
                            if (!$current_setting = strval($ui->getOption($id)))
                                $current_setting = '0';
                            ?>
                            <div>
                                <?php
                                esc_html_e('Specified element IDs also require the following site-wide Role:', 'press-permit-core');

                                $admin_caption = (!empty($custom_content_admin_cap)) ? esc_html__('Content Administrator', 'press-permit-core') : PWP::__wp('Administrator');

                                $captions = [
                                    '0' => esc_html__('no requirement', 'press-permit-core'), 
                                    '1' => esc_html__('Contributor / Author / Editor', 'press-permit-core'), 
                                    'author' => esc_html__('Author / Editor', 'press-permit-core'), 
                                    'editor' => PWP::__wp('Editor'), 
                                    'admin_content' => esc_html__('Content Administrator', 'press-permit-core'), 
                                    'admin_user' => esc_html__('User Administrator', 'press-permit-core'), 
                                    'admin_option' => esc_html__('Option Administrator', 'press-permit-core')
                                ];

                                foreach ($captions as $key => $value) {
                                    $key = strval($key);
                                    echo "<div style='margin: 0 0 0.5em 2em;'><label for='" . esc_attr("{$id}_{$key}") . "'>";
                                    $checked = ($current_setting === $key) ? ' checked ' : '';

                                    echo "<input name='" . esc_attr($id) . "' type='radio' id='" . esc_attr("{$id}_{$key}") . "' value='" . esc_attr($key) . "' " . esc_attr($checked) . " /> ";
                                    echo esc_html($value);
                                    echo '</label></div>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endif; // any options accessable in this section
        }

        $section = 'media_library';                                        // --- MEDIA LIBRARY SECTION ---
        if (!empty($ui->form_options[$tab][$section])) :
            ?>
            <tr>
                <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                <td>
                    <?php

                    if (defined('PP_MEDIA_LIB_UNFILTERED')) :
                        ?>
                        <div><span class="pp-important">
                            <?php SettingsAdmin::echoStr('media_lib_unfiltered'); ?>
                        </span></div><br />
                    <?php else : ?>
                        <div><span style="font-weight:bold">
                            <?php esc_html_e('The following settings apply to users who have the upload_files or edit_files capability:', 'press-permit-core'); ?>
                        </span></div><br />
                    <?php endif;

                    $ret = $ui->optionCheckbox('admin_others_attached_to_readable', $tab, $section, true, '');

                    $ret = $ui->optionCheckbox('admin_others_attached_files', $tab, $section, true, '');

                    $ret = $ui->optionCheckbox('edit_others_attached_files', $tab, $section, true, '');

                    $ret = $ui->optionCheckbox('admin_others_unattached_files', $tab, $section, true, '');

                    $ret = $ui->optionCheckbox('own_attachments_always_editable', $tab, $section, true, '');
                    ?>
                </td>
            </tr>
        <?php endif; // any options accessable in this section


        $section = 'nav_menu_management';                                // --- NAV MENU MANAGEMENT SECTION ---
        if (!empty($ui->form_options[$tab][$section])) : ?>
            <tr>
                <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                <td>
                    <?php
                    $ui->optionCheckbox('admin_nav_menu_filter_items', $tab, $section, '', '', ['val' => true, 'disabled' => true]);

                    $ui->optionCheckbox('admin_nav_menu_partial_editing', $tab, $section, true, '');

                    $ui->optionCheckbox('admin_nav_menu_lock_custom', $tab, $section, true, '');
                    ?>
                </td>
            </tr>
        <?php endif; // any options accessable in this section


        $section = 'user_management';                                    // --- USER MANAGEMENT SECTION ---
        if (!empty($ui->form_options[$tab][$section])) : ?>
            <tr>
                <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                <td>
                    <?php
                    $option_name = 'limit_user_edit_by_level';
                    $ui->all_options[]= $option_name;
                    if (!$option_val = $ui->getOption($option_name)) {
                        $option_val = '0';
                    }

                    esc_html_e('User editing capabilities apply for', 'press-permit-core');
                    echo "&nbsp;<select name='" . esc_attr($option_name) . "' id='" . esc_attr($option_name) . "' autocomplete='off'>";

                    $captions = ['0' => esc_html__("any user", 'press-permit-core'), '1' => esc_html__("equal or lower role levels", 'press-permit-core'), 'lower_levels' => esc_html__("lower role levels", 'press-permit-core')];
                    foreach ($captions as $key => $value) {
                        $selected = ($option_val == $key) ? 'selected="' : '';
                        echo "\n\t<option value='" . esc_attr($key) . "' " . esc_attr($selected) . ">" . esc_html($captions[$key]) . "</option>";
                    }
                    ?>
                    </select>&nbsp;

                    <div class='pp-subtext'>
                    <?php
                    SettingsAdmin::echoStr('limit_user_edit_by_level');
                    ?>
                    </div>

                    <div style="margin-top:20px">
                    <?php
                    $ui->optionCheckbox('add_author_pages', $tab, $section, true, '');

                    $div_style = ($pp->getOption('add_author_pages')) ? '' : 'display:none';
                    $ui->optionCheckbox('publish_author_pages', $tab, $section, '', '', compact('div_style'));
                    ?>
                    </div>
                </td>
            </tr>
        <?php endif; // any options accessable in this section


        if (class_exists('Fork', false)) {
            $section = 'post_forking';                                        // --- POST FORKING SECTION ---
            if (!empty($ui->form_options[$tab][$section])) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($ui->section_captions[$tab][$section]); ?></th>
                    <td>
                        <?php
                        $ui->optionCheckbox('fork_published_only', $tab, $section, true, '');

                        $ui->optionCheckbox('fork_require_edit_others', $tab, $section, true, '');
                        ?>
                    </td>
                </tr>
            <?php endif; // any options accessable in this section
        }
    }

    function advanced_tab_permissions_options_ui($tab, $section)
    {
        if (('advanced' == $tab) && ('permissions_admin' == $section)) {
            \PublishPress\Permissions\UI\SettingsAdmin::instance()->optionCheckbox('non_admins_set_edit_exceptions', 'advanced', 'permissions_admin', true);

            \PublishPress\Permissions\UI\SettingsAdmin::instance()->optionCheckbox('publish_exceptions', $tab, $section, '');
        }
    }

    function flt_cap_descriptions($pp_caps)
    {
        return SettingsAdmin::setCapabilityDescriptions($pp_caps);
    }
}
