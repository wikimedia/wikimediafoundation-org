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

namespace Inpsyde\MultilingualPress\NavMenu;

use function Inpsyde\MultilingualPress\assignedLanguages;
use function Inpsyde\MultilingualPress\siteExists;

class ItemRepository
{
    const META_KEY_SITE_ID = '_blog_id';
    const META_KEY_ITEM_TYPE = '_menu_item_type';
    const ITEM_TYPE = 'mlp_language';
    const FILTER_MENU_LANGUAGE_NAME = 'multilingualpress.nav_menu_language_name';

    /**
     * @var int[]
     */
    private $siteIds = [];

    /**
     * Returns the according items for the sites with the given IDs.
     *
     * @param int $menuId
     * @param int[] $siteIds
     * @return \WP_Post[]
     */
    public function itemsForSites(int $menuId, int ...$siteIds): array
    {
        if (!$menuId || !$siteIds) {
            return [];
        }

        $languageNames = assignedLanguages();
        $items = [];

        foreach ($siteIds as $siteId) {
            if (empty($languageNames[$siteId]) || !siteExists($siteId)) {
                continue;
            }

            $item = $this->ensureItem(
                $menuId,
                $siteId,
                $languageNames[$siteId]->name()
            );

            if ($item) {
                $items[] = $this->prepareItem($item, $siteId);
            }
        }

        return $items;
    }

    /**
     * Returns the site ID for the nav menu item with the given ID.
     *
     * @param int $itemId
     * @return int
     */
    public function siteIdOfMenuItem(int $itemId): int
    {
        if (isset($this->siteIds[$itemId])) {
            return $this->siteIds[$itemId];
        }

        $siteId = (int)get_post_meta(
            $itemId,
            ItemRepository::META_KEY_SITE_ID,
            true
        );

        $this->siteIds[$itemId] = $siteId;

        return $siteId;
    }

    /**
     * Ensures that an item according to the given arguments exists in the database.
     *
     * @param int $menuId
     * @param int $siteId
     * @param string $languageName
     * @return \WP_Post|null
     */
    private function ensureItem(int $menuId, int $siteId, string $languageName)
    {
        $title = (string) apply_filters(
            self::FILTER_MENU_LANGUAGE_NAME,
            $languageName,
            $menuId,
            $siteId
        );

        $post = get_post(
            wp_update_nav_menu_item(
                $menuId,
                0,
                [
                    'menu-item-title' => esc_attr($title),
                    'menu-item-url' => get_home_url($siteId, '/'),
                ]
            )
        );

        return $post instanceof \WP_Post ? $post : null;
    }

    /**
     * Prepares the given item for use.
     *
     * @param \WP_Post|\stdClass $item
     * @param int $siteId
     * @return \WP_Post
     */
    private function prepareItem(\WP_Post $item, int $siteId): \WP_Post
    {
        $item->object = self::ITEM_TYPE;
        $item->url = get_home_url($siteId, '/');
        if (empty($item->classes) || !is_array($item->classes)) {
            $item->classes = [];
        }
        $item->classes = array_filter($item->classes);
        $item->classes[] = "site-id-{$siteId}";
        $item->classes[] = 'mlp-language-nav-item';
        $item->xfn = 'alternate';

        update_post_meta($item->ID, self::META_KEY_SITE_ID, $siteId);
        update_post_meta($item->ID, self::META_KEY_ITEM_TYPE, self::ITEM_TYPE);

        return wp_setup_nav_menu_item($item);
    }
}
