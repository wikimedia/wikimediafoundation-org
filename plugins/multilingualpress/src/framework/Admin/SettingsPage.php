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

class SettingsPage
{
    const ADMIN_NETWORK = 1;
    const ADMIN_SITE = 0;
    const ADMIN_USER = 2;
    const PARENT_APPEARANCE = 'themes.php';
    const PARENT_COMMENTS = 'edit-comments.php';
    const PARENT_DASHBOARD = 'index.php';
    const PARENT_LINKS = 'link-manager.php';
    const PARENT_MEDIA = 'upload.php';
    const PARENT_NETWORK_SETTINGS = 'settings.php';
    const PARENT_PAGES = 'edit.php?post_type=page';
    const PARENT_PLUGINS = 'plugins.php';
    const PARENT_POSTS = 'edit.php';
    const PARENT_SETTINGS = 'options-general.php';
    const PARENT_SITES = 'sites.php';
    const PARENT_THEMES = 'themes.php';
    const PARENT_TOOLS = 'tools.php';
    const PARENT_USER_PROFILE = 'profile.php';
    const PARENT_USERS = 'users.php';
    const PARENT_MULTILINGUALPRESS = 'multilingualpress';

    /**
     * @var \stdClass
     */
    private $args;

    /**
     * @var string
     */
    private $hookName = '';

    /**
     * @var string
     */
    private $parent = '';

    /**
     * @var string
     */
    private $url;

    /**
     * @param int $admin
     * @param string $pageTitle
     * @param string $menuTitle
     * @param string $capability
     * @param string $menuSlug
     * @param SettingsPageView $view
     * @param string $iconUrl
     * @param int|null $position
     */
    public function __construct(
        int $admin,
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $menuSlug,
        SettingsPageView $view,
        string $iconUrl = '',
        int $position = null
    ) {

        $this->args = (object)compact(
            'admin',
            'pageTitle',
            'menuTitle',
            'capability',
            'menuSlug',
            'view',
            'iconUrl',
            'position'
        );
    }

    /**
     * @param int $admin
     * @param string $parent
     * @param string $pageTitle
     * @param string $menuTitle
     * @param string $capability
     * @param string $menuSlug
     * @param SettingsPageView $view
     * @return SettingsPage
     */
    public static function withParent(
        int $admin,
        string $parent,
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $menuSlug,
        SettingsPageView $view
    ): SettingsPage {

        $settingsPage = new static(
            $admin,
            $pageTitle,
            $menuTitle,
            $capability,
            $menuSlug,
            $view
        );

        $settingsPage->parent = $parent;

        return $settingsPage;
    }

    /**
     * @return string
     */
    public function capability(): string
    {
        return $this->args->capability;
    }

    /**
     * @return string
     */
    public function hookName(): string
    {
        return $this->hookName ?: '';
    }

    /**
     * @return bool
     */
    public function register(): bool
    {
        $action = $this->action();
        if ($action) {
            add_action($action, $this->callback());
        }

        return (bool)$action;
    }

    /**
     * @return string
     */
    public function menuSlug(): string
    {
        return $this->args->menuSlug;
    }

    /**
     * @return string
     */
    public function pageTitle(): string
    {
        return $this->args->pageTitle;
    }

    /**
     * Returns the full URL.
     *
     * @return string
     */
    public function url(): string
    {
        if (is_string($this->url)) {
            return $this->url;
        }

        $url = add_query_arg(
            'page',
            $this->args->menuSlug,
            $this->parent ?: 'admin.php'
        );

        switch ($this->args->admin) {
            case static::ADMIN_NETWORK:
                $this->url = network_admin_url($url);
                break;
            case static::ADMIN_SITE:
                $this->url = admin_url($url);
                break;
            case static::ADMIN_USER:
                $this->url = user_admin_url($url);
                break;
        }

        return $this->url;
    }

    /**
     * Returns the action for registering the page.
     *
     * @return string
     */
    private function action(): string
    {
        switch ($this->args->admin) {
            case static::ADMIN_NETWORK:
                return 'network_admin_menu';

            case static::ADMIN_SITE:
                return 'admin_menu';

            case static::ADMIN_USER:
                return 'user_admin_menu';
        }

        return '';
    }

    /**
     * Returns the callback for adding the page to the admin menu.
     *
     * @return callable
     */
    private function callback(): callable
    {
        if ($this->parent) {
            return function () {
                $this->hookName = add_submenu_page(
                    $this->parent,
                    $this->args->pageTitle,
                    $this->args->menuTitle,
                    $this->args->capability,
                    $this->args->menuSlug,
                    [$this->args->view, 'render']
                );
            };
        }

        return function () {
            $this->hookName = add_menu_page(
                $this->args->pageTitle,
                $this->args->menuTitle,
                $this->args->capability,
                $this->args->menuSlug,
                [$this->args->view, 'render'],
                $this->args->iconUrl,
                $this->args->position
            );
        };
    }
}
