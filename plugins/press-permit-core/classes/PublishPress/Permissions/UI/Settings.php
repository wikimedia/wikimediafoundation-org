<?php

namespace PublishPress\Permissions\UI;

class Settings
{
    public function __construct() {
        // called by Dashboard\DashboardFilters::actMenuHandler

        @load_plugin_textdomain('press-permit-core-hints', false, dirname(plugin_basename(PRESSPERMIT_FILE)) . '/languages');

        require_once(PRESSPERMIT_CLASSPATH . '/UI/SettingsAdmin.php');

        require_once(PRESSPERMIT_CLASSPATH . '/UI/SettingsTabModules.php');
        new SettingsTabModules();

        require_once(PRESSPERMIT_CLASSPATH . '/UI/SettingsTabCore.php');
        new SettingsTabCore();

        require_once(PRESSPERMIT_CLASSPATH . '/UI/SettingsTabAdvanced.php');
        new SettingsTabAdvanced();

        require_once(PRESSPERMIT_CLASSPATH . '/UI/SettingsTabInstall.php');
        new SettingsTabInstall();

        // enqueue JS for footer
        global $wp_scripts;
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
        wp_enqueue_script('presspermit-settings', PRESSPERMIT_URLPATH . "/common/js/settings{$suffix}.js", ['jquery', 'jquery-form'], PRESSPERMIT_VERSION, true);
        $wp_scripts->in_footer[] = 'presspermit-settings';  // otherwise it will not be printed in footer  todo: review

        $vars = [
            'displayHints' => presspermit()->getOption('display_hints'), 
            'hintImg' => plugins_url('', PRESSPERMIT_FILE) . "/common/img/comment-grey-bubble.png"
        ];
        wp_localize_script('presspermit-settings', 'ppCoreSettings', $vars);

        if (presspermit()->isPro()) {
            wp_enqueue_script('presspermit-pro-settings', plugins_url('', PRESSPERMIT_PRO_FILE) . "/includes-pro/settings-pro{$suffix}.js", ['jquery', 'jquery-form'], PRESSPERMIT_PRO_VERSION, true);
            $wp_scripts->in_footer[] = 'presspermit-pro-settings';  // otherwise it will not be printed in footer  todo: review
        }

        if (!current_user_can('pp_manage_settings'))
            wp_die(esc_html(PWP::__wp('Cheatin&#8217; uh?')));

        do_action('presspermit_options_ui');

        $ui = SettingsAdmin::instance();

        $ui->all_options = [];

        $ui->tab_captions = apply_filters('presspermit_option_tabs', []);
        $ui->section_captions = apply_filters('presspermit_section_captions', []);
        $ui->option_captions = apply_filters('presspermit_option_captions', []);
        $ui->form_options = apply_filters('presspermit_option_sections', []);

        $ui->display_hints = presspermit()->getOption('display_hints');

        if ($_hidden = apply_filters('presspermit_hide_options', [])) {
            $hidden = [];
            foreach (array_keys($_hidden) as $option_name) {
                if (!is_array($_hidden[$option_name]) && strlen($option_name) > 3)
                    $hidden[] = substr($option_name, 3);
            }

            foreach (array_keys($ui->form_options) as $tab) {
                foreach (array_keys($ui->form_options[$tab]) as $section)
                    $ui->form_options[$tab][$section] = array_diff($ui->form_options[$tab][$section], $hidden);
            }
        }

        ?>
        <div class="pressshack-admin-wrapper wrap" id="pp-permissions-wrapper">
            <?php
            echo '<form id="pp_settings_form" action="" method="post">';
            wp_nonce_field('pp-update-options');

            do_action('presspermit_options_form');
            ?>
        <header>
            <?php PluginPage::icon(); ?>
            <h1>
                <?php
                echo esc_html(apply_filters('presspermit_options_form_title', __('Permissions Settings', 'press-permit-core')));
                ?>
            </h1>

            <?php

            if ($subheading = apply_filters('presspermit_options_form_subheading', '')) {
                echo esc_html($subheading);
            }

            $class_selected = "nav-tab nav-tab-active";
            $class_unselected = "nav-tab";
            ?>


        </header>
        
            <?php
            $default_tab = presspermit_REQUEST_key('pp_tab');

            if (!isset($ui->tab_captions[$default_tab])) {
                $default_tab = 'modules';
            }

            $default_tab = apply_filters('presspermit_options_default_tab', $default_tab);

            // todo: prevent line breaks in these links
            echo "<ul class='nav-tab-wrapper' style='margin-bottom:-0.1em'>";

            foreach ($ui->tab_captions as $tab => $caption) {
                if (!empty($ui->form_options[$tab])) {
                    $class = ($default_tab == $tab) ? $class_selected : $class_unselected;  // todo: return to last tab

                    echo "<li class='" . esc_attr($class) . "'><a href='#pp-" . esc_attr($tab) . "'>"
                        . esc_html($ui->tab_captions[$tab]) . '</a></li>';
                }
            }
            echo '</ul>';

            echo '<div class="pp-options-wrapper">';
            $table_class = 'form-table pp-form-table pp-options-table';

            if (presspermit_is_REQUEST('presspermit_submit') || presspermit_is_REQUEST('presspermit_submit_redirect')) :
                ?>
                <div id="message" class="updated">
                    <p>
                        <strong><?php esc_html_e('All settings were updated.', 'press-permit-core'); ?>&nbsp;</strong>
                    </p>
                </div>
            <?php
            elseif (presspermit_is_REQUEST('presspermit_defaults')) :
                ?>
                <div id="message" class="updated">
                    <p>
                        <strong><?php esc_html_e('All settings were reset to defaults.', 'press-permit-core'); ?>&nbsp;</strong>
                    </p>
                </div>
            <?php
            endif;

            foreach (array_keys($ui->tab_captions) as $tab) {
                $display = ($default_tab == $tab) ? '' : 'display:none';
                echo "<div id='pp-" . esc_attr($tab) . "' style='clear:both;margin:0;" . esc_attr($display) . "' class='pp-options'>";

                do_action("presspermit_" . esc_attr($tab) . "_options_pre_ui");

                echo "<table class='" . esc_attr($table_class) . "' id='pp-" . esc_attr($tab) . "_table'>";

                do_action("presspermit_" . esc_attr($tab) . "_options_ui");

                echo '</table></div>';
            }

            echo '</div>'; // pp-options-wrapper

            $ui->filterNetworkOptions();

            echo "<input type='hidden' name='all_options' value='" . esc_attr(implode(',', $ui->all_options)) . "' />";
            echo "<input type='hidden' name='all_otype_options' value='" . esc_attr(implode(',', $ui->all_otype_options)) . "' />";

            echo "<input type='hidden' name='pp_submission_topic' value='options' />";
            ?>

            <div class="submit pp-submit" style="border:none;">
                <input type="submit" name="presspermit_submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'press-permit-core'); ?>"/>

                <input type="hidden" name="pp_tab"
                        value="<?php if ($pp_tab = presspermit_REQUEST_key('pp_tab')) echo esc_attr($pp_tab); ?>"/>
            </div>
            </form>
            <p style='clear:both'></p>

            <?php 
            presspermit()->admin()->publishpressFooter();
            ?>
        </div>

        <?php
    }

    public static function pluginInfoURL($plugin_slug)
    {
        return self_admin_url("plugin-install.php?tab=plugin-information&plugin=$plugin_slug&TB_iframe=true&width=640&height=678");
    }
}