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

/**
 * Class CacheSettingsOptions
 * @package Inpsyde\MultilingualPress\Core\Admin
 */
class CacheSettingsOptions
{
    const OPTION_GROUP_API_NAME = 'api';
    const OPTION_GROUP_DATABASE_NAME = 'database';
    const OPTION_GROUP_NAV_MENU_NAME = 'nav_menu';

    const OPTION_SEARCH_TRANSLATIONS_API_NAME = 'api.translation';
    const OPTION_CONTENT_IDS_API_NAME = 'api.content_ids';
    const OPTION_RELATIONS_API_NAME = 'api.content_relations';
    const OPTION_HAS_SITE_RELATIONS_API_NAME = 'api.has_site_relations';
    const OPTION_ALL_RELATIONS_API_NAME = 'api.all_relations';
    const OPTION_RELATED_SITE_IDS_API_NAME = 'api.related_site_ids';

    const OPTION_ALL_TABLES_DATABASE_NAME = 'database.table_list';

    const OPTION_ITEM_FILTER_NAV_MENU_NAME = 'nav_menu.item_filter';

    /**
     * Retrieve Default Options
     *
     * Default options are also the list of the options it self not just default values.
     *
     * @return array
     */
    public function defaults(): array
    {
        return [
            self::OPTION_GROUP_API_NAME => [
                self::OPTION_SEARCH_TRANSLATIONS_API_NAME => true,
                self::OPTION_CONTENT_IDS_API_NAME => true,
                self::OPTION_RELATIONS_API_NAME => true,
                self::OPTION_HAS_SITE_RELATIONS_API_NAME => true,
                self::OPTION_ALL_RELATIONS_API_NAME => true,
                self::OPTION_RELATED_SITE_IDS_API_NAME => true,
            ],
            self::OPTION_GROUP_DATABASE_NAME => [
                self::OPTION_ALL_TABLES_DATABASE_NAME => true,
            ],
            self::OPTION_GROUP_NAV_MENU_NAME => [
                self::OPTION_ITEM_FILTER_NAV_MENU_NAME => true,
            ],
        ];
    }

    /**
     * Retrieve Information about the options such as
     * - Group Name
     * - Label for specific option
     * - Description for specific option
     *
     * @return array
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function info(): array
    {
        // phpcs:enable

        return [
            self::OPTION_GROUP_API_NAME => [
                'name' => esc_html_x('Api', 'Cache Settings', 'multilingualpress'),
                'options' => [
                    self::OPTION_SEARCH_TRANSLATIONS_API_NAME => [
                        'label' => esc_html__('Translations', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache the Search for Translations. This will prevent to query the database every time we are looking for a translation for contents.',
                            'multilingualpress'
                        ),
                    ],
                    self::OPTION_CONTENT_IDS_API_NAME => [
                        'label' => esc_html__('Content Ids', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache the content ids when we are looking for translated content. Prevent to do extra queries to the database for known translated content ids.',
                            'multilingualpress'
                        ),
                    ],
                    self::OPTION_RELATIONS_API_NAME => [
                        'label' => esc_html__('Content Relations', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache site relations for specific content ids, similar to Content Ids but also prevent to do a query that use JOIN to retrieve the relationships for specific content.',
                            'multilingualpress'
                        ),
                    ],
                    self::OPTION_HAS_SITE_RELATIONS_API_NAME => [
                        'label' => esc_html__('Has Site Relations', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache results of query used to know if a specific site has a specific type of relationship.',
                            'multilingualpress'
                        ),
                    ],
                    self::OPTION_ALL_RELATIONS_API_NAME => [
                        'label' => esc_html__('All Relations', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache all relations query result. All relations include all content relations. Boost performances because of the big query needed to retrieve all of the content relations.',
                            'multilingualpress'
                        ),
                    ],
                    self::OPTION_RELATED_SITE_IDS_API_NAME => [
                        'label' => esc_html__('Related Site Ids', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache query result for IDs of all sites related to the site with the given ID.',
                            'multilingualpress'
                        ),
                    ],
                ],
            ],
            self::OPTION_GROUP_DATABASE_NAME => [
                'name' => esc_html_x('Database', 'Cache Settings', 'multilingualpress'),
                'options' => [
                    self::OPTION_ALL_TABLES_DATABASE_NAME => [
                        'label' => esc_html__('Table List', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache all tables query result. Basically a query that will return all of the existings table within the database.',
                            'multilingualpress'
                        ),
                    ],
                ],
            ],
            self::OPTION_GROUP_NAV_MENU_NAME => [
                'name' => esc_html_x('Navigation Menu', 'Cache Settings', 'multilingualpress'),
                'options' => [
                    self::OPTION_ITEM_FILTER_NAV_MENU_NAME => [
                        'label' => esc_html__('Menu Items', 'multilingualpress'),
                        'description' => esc_html__(
                            'Cache all navigation menu items for a faster access. This improve speed on build the navigation menu list when it\'s rendered in frontend',
                            'multilingualpress'
                        ),
                    ],
                ],
            ],
        ];
    }
}
