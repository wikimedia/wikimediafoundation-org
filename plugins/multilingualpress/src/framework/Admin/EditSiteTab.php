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

namespace Inpsyde\MultilingualPress\Framework\Admin;

use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Tab for all Edit Site pages.
 */
class EditSiteTab
{

    /**
     * @var SettingsPageTabData
     */
    private $tabData;

    /**
     * @var SettingsPage
     */
    private $settingsPage;

    /**
     * @param SettingsPageTab $tab
     */
    public function __construct(SettingsPageTab $tab)
    {
        $this->tabData = $tab->data();
        $this->settingsPage = SettingsPage::withParent(
            SettingsPage::ADMIN_NETWORK,
            SettingsPage::PARENT_SITES,
            $tab->title(),
            '',
            $tab->capability(),
            $tab->slug(),
            $tab->view()
        );
    }

    /**
     * Registers both the tab and the settings page for the tab.
     *
     * @return bool
     */
    public function register(): bool
    {
        if (!$this->registerSettingPage()) {
            return false;
        }

        add_action(
            'network_admin_menu',
            function () {
                $this->removeSubmenuPage();
                $this->fillGlobals();
                $this->filterNetworkAdminLinks();
            },
            20
        );

        return true;
    }

    /**
     * @return bool
     */
    private function registerSettingPage(): bool
    {
        if (did_action('adminmenu')) {
            return false;
        }

        return $this->settingsPage->register();
    }

    /**
     * @return void
     */
    private function removeSubmenuPage()
    {
        remove_submenu_page(
            SettingsPage::PARENT_SITES,
            $this->settingsPage->menuSlug()
        );
    }

    /**
     * @return void
     */
    private function fillGlobals()
    {
        add_action(
            'load-' . $this->settingsPage->hookName(),
            static function () {
                $GLOBALS['parent_file'] = SettingsPage::PARENT_SITES;
                $GLOBALS['submenu_file'] = SettingsPage::PARENT_SITES;
            }
        );
    }

    /**
     * @return void
     */
    private function filterNetworkAdminLinks()
    {
        add_filter(
            'network_edit_site_nav_links',
            wpHookProxy(function (array $links = []): array {
                $links[$this->tabData->id()] = $this->tabLinkData();

                return $links;
            })
        );
    }

    /**
     * @return array
     */
    private function tabLinkData(): array
    {
        return [
            'label' => esc_html($this->tabData->title()),
            'cap' => $this->tabData->capability(),
            'url' => add_query_arg(
                'page',
                $this->tabData->slug(),
                SettingsPage::PARENT_SITES
            ),
        ];
    }
}
