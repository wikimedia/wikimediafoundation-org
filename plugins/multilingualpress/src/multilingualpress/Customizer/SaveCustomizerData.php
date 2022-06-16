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

namespace Inpsyde\MultilingualPress\Customizer;

use function Inpsyde\MultilingualPress\siteExists;

class SaveCustomizerData implements SaveCustomizerDataInterface
{
    /**
     * if there are language items in the changed data of customizer then update menu item meta values
     * @param array $changeSetData
     */
    public function updateCustomizerMenuData(array $changeSetData)
    {
        foreach ($changeSetData as $data) {
            if ($this->isLanguageItemExists($data)) {
                $this->updateMenuItemMeta($data);
            }
        }
    }

    /**
     * Check if there are language items in the changed data of customizer
     *
     * @param array $data customizer's changed data item
     * @return bool
     */
    protected function isLanguageItemExists(array $data): bool
    {
        if (
            !empty($data['value'])
            && !empty($data['value']['object'])
            && !empty($data['value']['type']) && $data['value']['type'] === 'mlp_language'
            && !empty($data['value']['site_id']) && siteExists($data['value']['site_id'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * update menu item meta values Which are necessary for passing the proper url when
     * wp_nav_menu_objects will be called in frontend
     *
     * @param array $data customizer's changed language data item
     */
    protected function updateMenuItemMeta(array $data)
    {
        if (empty($data['value']['nav_menu_term_id']) || empty($data['value']['object_id'])) {
            return;
        }
        $menuItems = wp_get_nav_menu_items($data['value']['nav_menu_term_id']);
        foreach ($menuItems as $item) {
            if (!isset($item->type) || !isset($item->object_id) || !isset($item->ID)) {
                return;
            }
            if ($item->type === 'mlp_language' && (int)$item->object_id === (int)$data['value']['object_id']) {
                update_post_meta($item->ID, '_blog_id', $data['value']['site_id']);
                update_post_meta($item->ID, '_menu_item_type', $data['value']['type']);
            }
        }
    }
}
