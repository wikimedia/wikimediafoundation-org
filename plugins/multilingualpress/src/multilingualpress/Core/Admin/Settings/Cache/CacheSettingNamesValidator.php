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

namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * Class CacheOptionNamesValidator
 * @package Inpsyde\MultilingualPress\Core\Admin\Settings\Cache
 */
class CacheSettingNamesValidator
{
    const ALLOWED_OPTIONS = [
        CacheSettingsOptions::OPTION_GROUP_API_NAME => [
            CacheSettingsOptions::OPTION_SEARCH_TRANSLATIONS_API_NAME,
            CacheSettingsOptions::OPTION_CONTENT_IDS_API_NAME,
            CacheSettingsOptions::OPTION_RELATIONS_API_NAME,
            CacheSettingsOptions::OPTION_HAS_SITE_RELATIONS_API_NAME,
            CacheSettingsOptions::OPTION_ALL_RELATIONS_API_NAME,
            CacheSettingsOptions::OPTION_RELATED_SITE_IDS_API_NAME,
        ],
        CacheSettingsOptions::OPTION_GROUP_DATABASE_NAME => [
            CacheSettingsOptions::OPTION_ALL_TABLES_DATABASE_NAME,
        ],
        CacheSettingsOptions::OPTION_GROUP_NAV_MENU_NAME => [
            CacheSettingsOptions::OPTION_ITEM_FILTER_NAV_MENU_NAME,
        ],
    ];

    /**
     * @param array $settings
     * @return bool
     */
    public function allowed(array $settings): bool
    {
        $allowedOptionsKeys = iterator_to_array(
            new RecursiveIteratorIterator(new RecursiveArrayIterator(self::ALLOWED_OPTIONS)),
            false
        );

        $settingsFlat = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($settings)
        );

        foreach ($settingsFlat as $key => $value) {
            if (!\in_array($key, $allowedOptionsKeys, true)) {
                return false;
            }
        }

        return true;
    }
}
