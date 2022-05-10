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

namespace Inpsyde\MultilingualPress\Core\Admin;

/**
 * Class Screen
 * @package Inpsyde\MultilingualPress\Core\Admin
 */
class Screen
{
    private static $screen;

    /**
     * @return bool
     */
    public static function isNetworkSite(): bool
    {
        return (
            self::currentScreen() &&
            (
                self::$screen->id === 'site-new-network'
                || self::$screen->id === 'site-new-network'
                || self::$screen->id === 'sites_page_multilingualpress-site-settings-network'
            )
        );
    }

    /**
     * @return bool
     */
    public static function isNetworkNewSite(): bool
    {
        return (
            self::currentScreen() && self::$screen->id === 'site-new-network'
        );
    }

    /**
     * @return bool
     */
    public static function isMultilingualPressSettings(): bool
    {
        return (
            self::currentScreen() &&
            self::$screen->id === 'toplevel_page_multilingualpress-network'
        );
    }

    /**
     * @return bool
     */
    public static function isEditPostsTable(): bool
    {
        return (
            self::currentScreen() &&
            self::$screen->id === 'edit-post' || self::$screen->id === 'edit-page'
        );
    }

    /**
     * @return bool
     */
    public static function isEditPost(): bool
    {
        return (
            self::currentScreen() &&
            self::$screen->id === 'post' || self::$screen->id === 'page'
        );
    }

    /**
     * @return bool
     */
    public static function isEditSite(): bool
    {
        return (
            self::currentScreen() &&
            self::$screen->id === 'sites_page_multilingualpress-site-settings-network'
        );
    }

    /**
     * @return bool
     */
    private static function currentScreen(): bool
    {
        self::$screen = get_current_screen();

        return (bool)self::$screen;
    }
}
