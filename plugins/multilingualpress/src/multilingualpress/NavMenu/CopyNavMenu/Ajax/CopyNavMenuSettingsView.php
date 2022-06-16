<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\NavMenu\CopyNavMenu\Ajax;

use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use WP_Term;

use function Inpsyde\MultilingualPress\assignedLanguages;
use function Inpsyde\MultilingualPress\siteNameWithLanguage;
use function Inpsyde\MultilingualPress\assignedLanguageNames;

class CopyNavMenuSettingsView
{
    const ACTION = 'multilingualpress_copy_nav_menu_settings_view';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @param Nonce $nonce
     */
    public function __construct(Nonce $nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * Handle AJAX request.
     * @throws NonexistentTable
     */
    public function handle()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_' . self::ACTION)) {
            wp_send_json_error('Invalid action.');
        }

        $assignedLanguages = assignedLanguageNames();
        if (empty($assignedLanguages)) {
            return;
        }

        wp_send_json_success($this->generateCopyNavMenuSettingsMarkup());
    }

    /**
     * Render a select of menu names
     * @throws NonexistentTable
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    protected function generateCopyNavMenuSettingsMarkup(): string
    {
        // phpcs:enable

        $assignedLanguages = assignedLanguages();

        $copyMenuSelectOptionGroupMarkup = '';
        $siteIdFieldMarkup = '';
        $allSiteMenus = [];

        foreach ($assignedLanguages as $siteId => $language) {
            if ($siteId === get_current_blog_id() || empty($siteId)) {
                continue;
            }

            switch_to_blog($siteId);

            $menus = wp_get_nav_menus();

            if (!empty($menus)) {
                $allSiteMenus[] = wp_get_nav_menus();
            }

            $siteIdFieldMarkup = $this->hiddenSiteIdFieldMarkup((int)$siteId);

            $copyMenuSelectOptionMarkup = '';

            foreach ($menus as $menu) {
                $assignedMenuLocationNames = $this->assignedMenuLocationNames($menu);
                $copyMenuSelectOptionMarkup .= $this->selectOptionMarkup(
                    (int)$menu->term_id,
                    esc_html($menu->name),
                    $assignedMenuLocationNames
                );
            }

            $copyMenuSelectOptionGroupMarkup .= $this->selectOptionGroupMarkup(
                (int)$siteId,
                $copyMenuSelectOptionMarkup
            );

            restore_current_blog();
        }

        if (empty($allSiteMenus)) {
            $copyMenuSettingsMarkupFormat = '<div class="manage-menus mlp-copy-menu-settings">%1$s</div>';
            return sprintf(
                $copyMenuSettingsMarkupFormat,
                __(
                    'MultilingualPress: There are no menus found on remote site(s) to copy',
                    'multilingualpress'
                )
            );
        }

        $copyMenuSettingsMarkupFormat = '<div class="manage-menus mlp-copy-menu-settings">
                                            <form
                                                action="%1$s"
                                                method="post" onsubmit="return confirm(\'%2$s\')">
                                            %3$s
                                            <select name="mlp_menu_to_copy" id="select-menu-to-copy">%4$s</select>
                                            %5$s
                                            %6$s
                                            <input type="submit" class="button" id="mlp-copy-menu-submit" value="%7$s">
                                            %8$s
                                            </form>
                                        </div>';

        return sprintf(
            $copyMenuSettingsMarkupFormat,
            esc_url(admin_url('nav-menus.php')),
            __(
                'Are you sure you want to copy the menu from remote site? The current menu will be overridden',
                'multilingualpress'
            ),
            $this->selectLabelMarkup(),
            $copyMenuSelectOptionGroupMarkup,
            $siteIdFieldMarkup,
            $this->hiddenCurrentMenuNameFieldMarkup(),
            __('Copy', 'multilingualpress'),
            $this->nonceFieldMarkup()
        );
    }

    /**
     * Will return assigned location names of given menu
     *
     * @param WP_Term $menu WP_Term object for Menu
     * @return array of menu location names
     */
    protected function assignedMenuLocationNames(WP_Term $menu): array
    {
        $locations = get_registered_nav_menus();
        $menuLocations = get_nav_menu_locations();
        $assignedLocations = [];
        if (!empty($menuLocations) && in_array($menu->term_id, $menuLocations, true)) {
            foreach (array_keys($menuLocations, $menu->term_id, true) as $menuLocationKey) {
                $assignedLocations[] = $locations[$menuLocationKey] ?? '';
            }
        }

        return $assignedLocations;
    }

    /**
     * Generate the copy menu select-box label markup
     *
     * @return string the markup of the copy menu select-box label
     */
    protected function selectLabelMarkup(): string
    {
        $copyMenuSelectLabelMarkupFormat = '<label for="select-menu-to-copy">%1$s</label>';
        return sprintf(
            $copyMenuSelectLabelMarkupFormat,
            __('MultilingualPress: Select a menu to copy: ', 'multilingualpress')
        );
    }

    /**
     * Generate the hidden input with the site id value of remote site
     *
     * @param int $siteId The remote site id
     *
     * @return string the markup of the hidden input with remote site id value
     */
    protected function hiddenSiteIdFieldMarkup(int $siteId): string
    {
        $siteIdFieldMarkupFormat =
            '<input type="hidden" name="remote_site_id" id="remote_site_id" value="%1$s"/>';
        return sprintf(
            $siteIdFieldMarkupFormat,
            (int)$siteId
        );
    }

    /**
     * Generate the hidden input with the current menu id
     *
     * @return string the markup of the hidden input with the current menu id
     */
    protected function hiddenCurrentMenuNameFieldMarkup(): string
    {
        return '<input type="hidden" name="current_menu_name" id="current_menu_name" value=""/>';
    }

    /**
     * Generate the markup of the select options with remote site menu's id as the option value
     * and the option name with combination of remote site menu's name and location if is assigned
     *
     * @param int $menuTermId The menu id of the remote site
     * @param string $menuName The manu name of the remote site
     * @param array $assignedMenuLocationNames the assigned menu location of the menu
     *
     * @return string the markup of the select-box options
     */
    protected function selectOptionMarkup(
        int $menuTermId,
        string $menuName,
        array $assignedMenuLocationNames
    ): string {

        if (empty($menuTermId)) {
            return '';
        }

        $copyMenuSelectOptionMarkupFormat = '<option value="%1$s">%2$s - %3$s</option>';
        return sprintf(
            $copyMenuSelectOptionMarkupFormat,
            $menuTermId,
            $menuName ?? '',
            $assignedMenuLocationNames ?
                esc_html(implode(', ', $assignedMenuLocationNames)) :
                __('Location is not selected', 'multilingualpress')
        );
    }

    /**
     * Generate the markup of the select options group with the data-site_id to which the menus belong,
     * with the site name as the group label and the options with the menus which belong to that site
     *
     * @param int $siteId The remote site id
     * @param string $selectGroupOptionsMarkup The markup of the options of the group
     *
     * @return string the markup of the Group of options
     * @throws NonexistentTable
     */
    protected function selectOptionGroupMarkup(int $siteId, string $selectGroupOptionsMarkup): string
    {
        $copyMenuSelectOptionGroupMarkupFormat = '<optgroup data-site_id="%1$s" label="%2$s">
                                                        %3$s
                                                    </optgroup>';
        return sprintf(
            $copyMenuSelectOptionGroupMarkupFormat,
            $siteId,
            siteNameWithLanguage($siteId),
            $selectGroupOptionsMarkup
        );
    }

    /**
     * Generate the Nonce field markup
     *
     * @return string the markup of the Nonce field
     */
    protected function nonceFieldMarkup(): string
    {
        $nonceFieldMarkupFormat = '<input type="hidden" name="%1$s" value="%2$s">';
        return sprintf(
            $nonceFieldMarkupFormat,
            esc_attr($this->nonce->action()),
            esc_attr((string)$this->nonce)
        );
    }
}
