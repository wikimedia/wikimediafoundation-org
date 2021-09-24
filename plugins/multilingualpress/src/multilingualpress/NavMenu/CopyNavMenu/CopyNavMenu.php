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

namespace Inpsyde\MultilingualPress\NavMenu\CopyNavMenu;

use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use WP_Post;

use function Inpsyde\MultilingualPress\callExit;
use function Inpsyde\MultilingualPress\siteExists;
use function Inpsyde\MultilingualPress\translationIds;

/**
 * Handler for nav menu AJAX requests.
 */
class CopyNavMenu
{
    /**
     * The Request param names
     */
    const REQUEST_VALUE_NAME_FOR_MENU_TO_COPY = 'mlp_menu_to_copy';
    const REQUEST_VALUE_NAME_FOR_REMOTE_SITE_ID = 'remote_site_id';
    const REQUEST_VALUE_NAME_FOR_CURRENT_MENU_NAME = 'current_menu_name';

    /**
     * MLP language menu item configs
     */
    const LANGUAGE_MENU_ITEM_META_KEY_SITE_ID = '_blog_id';
    const LANGUAGE_MENU_ITEM_META_KEY_ITEM_TYPE = '_menu_item_type';
    const LANGUAGE_MENU_ITEM_TYPE = 'mlp_language';

    /**
     * Configs to determinate and update parent menu item of copied menu
     */
    const REMOTE_MENU_ITEM_ID = 'remote_menu_item_id';
    const MENU_ITEM_META_KEY_PARENT_MENU_ITEM = '_menu_item_menu_item_parent';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Nonce $nonce
     * @param Request $request
     */
    public function __construct(
        Nonce $nonce,
        Request $request
    ) {

        $this->nonce = $nonce;
        $this->request = $request;
    }

    /**
     * Handles the copy of navigation menu from remote site
     * Will get the values from Request
     * Will delete the current menu items
     * Will copy the menu items from remote site
     */
    public function handleCopyNavMenu()
    {
        if (!current_user_can('edit_theme_options') || !$this->nonce->isValid()) {
            return ;
        }
        $remoteMenuId = (int)$this->getValueFromRequest(self::REQUEST_VALUE_NAME_FOR_MENU_TO_COPY);
        $remoteSiteId = (int)$this->getValueFromRequest(self::REQUEST_VALUE_NAME_FOR_REMOTE_SITE_ID);
        $currentMenuName = $this->getValueFromRequest(self::REQUEST_VALUE_NAME_FOR_CURRENT_MENU_NAME);

        switch_to_blog($remoteSiteId);
        $menuToBeCopied = $this->getMenuItems($remoteMenuId);
        $remoteMenu = wp_get_nav_menu_object($remoteMenuId);
        restore_current_blog();

        $currentMenu = get_term_by('name', $currentMenuName, 'nav_menu');
        $currentMenuId = $currentMenu ?
            $currentMenu->term_id :
            $this->createNewNavMenu($remoteMenu->name . ' Copy ');

        if (
            empty($remoteSiteId) ||
            !siteExists($remoteSiteId) ||
            empty($currentMenuId) ||
            empty($remoteMenuId)
        ) {
            return ;
        }

        if (!$menuToBeCopied) {
            return;
        }

        $this->deleteMenuItems($currentMenuId);
        $this->copyMenuItems($menuToBeCopied, $remoteSiteId, $currentMenuId);

        $args = ['menu' => $currentMenuId];
        //phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
        wp_safe_redirect(add_query_arg($args, admin_url('nav-menus.php')));

        callExit();
    }

    /**
     * Will return the value from request with the giben param name
     *
     * @param string $requestParamName The name of the Request param,
     * can be either self::REQUEST_VALUE_NAME_FOR_MENU_TO_COPY or
     * self::REQUEST_VALUE_NAME_FOR_REMOTE_SITE_ID or
     * self::REQUEST_VALUE_NAME_FOR_CURRENT_MENU_ID
     * @return string the value of request param
     */
    protected function getValueFromRequest(string $requestParamName): string
    {
        if (empty($requestParamName)) {
            return '';
        }

        return $this->request->bodyValue(
            $requestParamName,
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );
    }

    /**
     * Retrieves all menu items of a navigation menu.
     *
     * @param int $menuId The id of the menu to get
     * @return false|array $items Array of menu items, otherwise false.
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    protected function getMenuItems(int $menuId)
    {
        // phpcs:enable
        return wp_get_nav_menu_items($menuId);
    }

    /**
     * Will delete the menu items of given menu
     *
     * @param int $menuId The menu id from which the items should be deleted
     */
    protected function deleteMenuItems(int $menuId)
    {
        $menuItemsToDelete = $this->getMenuItems($menuId);

        if (!$menuItemsToDelete) {
            return;
        }

        foreach ($menuItemsToDelete as $item) {
            is_nav_menu_item($item->ID) && wp_delete_post($item->ID, true);
        }
    }

    /**
     * Will Copy the Menu Items from remote site for selected menu
     *
     * @param array $remoteMenu The Remote menu which is selected to be copied
     * @param int $remoteSiteId The Remote site id to which the selected menu to be copied belongs
     * @param int $sourceMenuId The Source menu id to whcih the items should be copied
     * @throws NonexistentTable
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    protected function copyMenuItems(array $remoteMenu, int $remoteSiteId, int $sourceMenuId)
    {
        // phpcs:enable

        if (empty($remoteMenu)) {
            return;
        }

        foreach ($remoteMenu as $remoteMenuItem) {
            switch ($remoteMenuItem->type) {
                case 'post_type':
                    $contentType = 'post';
                    break;
                case 'taxonomy':
                    $contentType = 'term';
                    break;
                default:
                    $contentType = $remoteMenuItem->object;
            }
            $translations = translationIds((int)$remoteMenuItem->object_id, $contentType, $remoteSiteId);

            $menuItemData = $this->generateNewMenuItemData(
                $remoteMenuItem,
                $translations[get_current_blog_id()] ?? 0
            );

            $menuItemDbId = wp_update_nav_menu_item($sourceMenuId, 0, $menuItemData);
            $this->updateSourceLanguageMenuItemMeta($remoteMenuItem, $remoteSiteId, $menuItemDbId);
            update_post_meta($menuItemDbId, self::REMOTE_MENU_ITEM_ID, $remoteMenuItem->ID);
        }

        $this->updateParentMenuItems($sourceMenuId);
    }

    /**
     * Will update the necessary metadata for mlp_language type menu items
     *
     * @param WP_Post $remoteMenuItem The menu item object from remote site
     * @param int $remoteSiteId The remote site id from where the menu item is copied
     * @param int $sourceMenuItemDbId The copied source menu item db id
     */
    protected function updateSourceLanguageMenuItemMeta(
        WP_Post $remoteMenuItem,
        int $remoteSiteId,
        int $sourceMenuItemDbId
    ) {

        if ($remoteMenuItem->type !== self::LANGUAGE_MENU_ITEM_TYPE) {
            return;
        }

        switch_to_blog($remoteSiteId);
        $remoteMenuItemBlogId = get_post_meta(
            $remoteMenuItem->ID,
            self::LANGUAGE_MENU_ITEM_META_KEY_SITE_ID,
            true
        );
        restore_current_blog();

        update_post_meta(
            $sourceMenuItemDbId,
            self::LANGUAGE_MENU_ITEM_META_KEY_SITE_ID,
            $remoteMenuItemBlogId
        );
        update_post_meta(
            $sourceMenuItemDbId,
            self::LANGUAGE_MENU_ITEM_META_KEY_ITEM_TYPE,
            $remoteMenuItem->type
        );
    }

    /**
     * Will generate the menu item data which should be created because of the menu copy
     * If there is a connected post in source site then it's data will be taken, otherwise
     * will be created a custom menu item with the url to remote post.
     *
     * @param WP_Post $remoteMenuItem The remote menu item which should be copied
     * @param int $sourceContentId The source post id, if exist it can be used to grab
     * additional info from it instead of taking from remote menu item
     * @return array of generated menu item data
     */
    protected function generateNewMenuItemData(
        WP_Post $remoteMenuItem,
        int $sourceContentId
    ): array {

        $menuItemData = [
            'menu-item-position' => $remoteMenuItem->menu_order,
            'menu-item-title' => $remoteMenuItem->title,
            'menu-item-url' => $remoteMenuItem->url,
            'menu-item-description' => $remoteMenuItem->description,
            'menu-item-attr-title' => $remoteMenuItem->post_title,
            'menu-item-target' => $remoteMenuItem->target,
            'menu-item-classes' => !empty($remoteMenuItem->classes) ?
                implode(', ', $remoteMenuItem->classes) :
                '',
            'menu-item-xfn' => $remoteMenuItem->xfn,
            'menu-item-status' => $remoteMenuItem->post_status,
        ];

        if ($this->hasParentMenuItem((int)$remoteMenuItem->menu_item_parent)) {
            $menuItemData['menu-item-parent-id'] = $remoteMenuItem->menu_item_parent;
        }

        if (!in_array($remoteMenuItem->type, ['post_type', 'post_type_archive', 'taxonomy'], true)) {
            $menuItemData['menu-item-type'] = $remoteMenuItem->type;
            $menuItemData['menu-item-object'] = $remoteMenuItem->object;
        }

        $sourceCotent = get_post($sourceContentId);
        if ($sourceCotent && !is_wp_error($sourceCotent)) {
            $menuItemData['menu-item-object-id'] = $sourceCotent->ID;
            $menuItemData['menu-item-title'] = $sourceCotent->post_title;
            $menuItemData['menu-item-url'] = get_the_permalink($sourceCotent->ID);
            $menuItemData['menu-item-object'] = $remoteMenuItem->object;
            $menuItemData['menu-item-type'] = $remoteMenuItem->type;
        }

        if ($remoteMenuItem->type === 'taxonomy') {
            $sourceCotent = get_term($sourceContentId);
            if ($sourceCotent && !is_wp_error($sourceCotent)) {
                $menuItemData['menu-item-object-id'] = $sourceCotent->term_id;
                $menuItemData['menu-item-title'] = $sourceCotent->name;
                $menuItemData['menu-item-url'] = get_the_permalink($sourceCotent->term_id);
                $menuItemData['menu-item-object'] = $remoteMenuItem->object;
                $menuItemData['menu-item-type'] = $remoteMenuItem->type;
            }
        }

        return $menuItemData;
    }

    /**
     * Check if menu item has parent
     *
     * @param int $parentMenuItemId the menu item id to check
     * @return bool true/false if menu item has parent or no
     */
    protected function hasParentMenuItem(int $parentMenuItemId): bool
    {
        return $parentMenuItemId !== 0;
    }

    /**
     * The method will update the parent menu item ids for the given menu
     *
     * @param int $menuId The menu Id for which to check and update parent menu item ids
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    protected function updateParentMenuItems(int $menuId)
    {
        // phpcs:enable

        $menu = wp_get_nav_menu_items($menuId);
        foreach ($menu as $menuItem) {
            if ($this->hasParentMenuItem((int)$menuItem->menu_item_parent)) {
                $args = [
                    'meta_query' => [
                        [
                            'key' => self::REMOTE_MENU_ITEM_ID,
                            'value' => $menuItem->menu_item_parent,
                        ],
                    ],
                    'post_type' => 'nav_menu_item',
                ];
                $posts = get_posts($args);
                if (empty($posts) || !$posts[0]->ID) {
                    return;
                }
                update_post_meta($menuItem->ID, self::MENU_ITEM_META_KEY_PARENT_MENU_ITEM, $posts[0]->ID);
            }
        }
    }

    /**
     * Will create a new navigation menu
     *
     * @param string $namePrefix The name prefix of new menu
     * @return int created menu ID
     */
    protected function createNewNavMenu(string $namePrefix): int
    {
        $newMenuId = wp_create_nav_menu(uniqid($namePrefix));

        if (!$newMenuId || is_wp_error($newMenuId)) {
            return 0;
        }

        $locations = get_nav_menu_locations();
        if (empty($locations)) {
            return $newMenuId;
        }

        $locations = array_map(static function () use ($newMenuId): int {
            return $newMenuId;
        }, $locations);
        set_theme_mod('nav_menu_locations', $locations);

        return $newMenuId;
    }
}
