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

use Inpsyde\MultilingualPress\Framework\Language\Language;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use WP_Customize_Manager;

use function Inpsyde\MultilingualPress\assignedLanguages;
use function Inpsyde\MultilingualPress\wpHookProxy;

class ServiceProvider implements BootstrappableServiceProvider
{

    /**
     * @inheritdoc
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        /**
         * Configuration for Language Menu.
         * @see https://developer.wordpress.org/reference/hooks/customize_nav_menu_available_item_types/
         */
        $container->share(
            'multilingualpress.NavMenuItems.Language',
            static function (): array {
                return [
                    'title' => __('Languages', 'multilingualpress'),
                    'type_label' => __('Language', 'multilingualpress'),
                    'type' => 'mlp_language',
                    'object' => 'mlp_language',
                ];
            }
        );

        $container->share(
            'multilingualpress.SaveCustomizerData',
            static function (): SaveCustomizerDataInterface {
                return new SaveCustomizerData();
            }
        );

        /**
         * Configuration factory for Customizer language items.
         *
         * @see https://developer.wordpress.org/reference/hooks/customize_nav_menu_available_items/
         * @param string $type Menu item type (post, taxonomy, etc)
         * @param string $objectName Menu item object name
         * @param int $siteId Site Id
         * @param Language $language The Language object
         * @return array Configuration for Customizer language items.
         */
        $container->share(
            'multilingualpress.CreateCustomizerLanguageItem',
            static function (): callable {
                return static function (string $type, string $objectName, int $siteId, Language $language): array {
                    return [
                        'id' => "{$objectName}-{$siteId}",
                        'site_id' => $siteId,
                        'object' => $objectName,
                        'object_id' => $siteId,
                        'type' => $type,
                        'type_label' => __('Language', 'multilingualpress'),
                        'title' => $language->name(),
                        'classes' => ["site-id-{$siteId} mlp-language-nav-item"],
                    ];
                };
            }
        );

        /**
         * will return all the languages of existing sites
         */
        $container->share(
            'multilingualpress.AllSiteLanguages',
            static function (): array {
                return assignedLanguages();
            }
        );

        //phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
        $container->extend('multilingualpress.events', static function ($prev, Container $container) {
            // phpcs:enable
            $saveCustomizerData = $container->get('multilingualpress.SaveCustomizerData');
            assert($saveCustomizerData instanceof SaveCustomizerDataInterface);
            $navMenuItemsLanguage = $container->get('multilingualpress.NavMenuItems.Language');
            $createCustomizerLanguageItem = $container->get('multilingualpress.CreateCustomizerLanguageItem');

            /**
             * Filters nav menu available item types and adds new "Language" type.
             *
             * @param array $itemTypes Available menu item types
             * @return array filtered menu item types
             */
            add_filter(
                'customize_nav_menu_available_item_types',
                wpHookProxy(static function (array $itemTypes) use ($navMenuItemsLanguage): array {
                    $itemTypes[] = $navMenuItemsLanguage;
                    return $itemTypes;
                })
            );

            /**
             * Filters nav menu available items and adds language items.
             *
             * @param array $items all menu available items
             * @param string $type menu item type
             * @param string $objectName menu item object name
             * @return array filtered menu items
             * @throws \Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable
             */
            add_filter(
                'customize_nav_menu_available_items',
                wpHookProxy(static function (
                    array $items,
                    string $type,
                    string $objectName
                ) use (
                    $createCustomizerLanguageItem,
                    $container
                ): array {

                    $allSiteLanguages = $container->get('multilingualpress.AllSiteLanguages');
                    if ($objectName !== 'mlp_language' || !$allSiteLanguages) {
                        return $items;
                    }

                    foreach ($allSiteLanguages as $siteId => $language) {
                        $items[] = $createCustomizerLanguageItem($type, $objectName, $siteId, $language);
                    }
                    return $items;
                }),
                10,
                4
            );

            /**
             * The filter will work after customizer settings are saved and will update menu item meta values
             * Which are necessary for passing the proper url when
             * wp_nav_menu_objects will be called in frontend
             *
             * @param \WP_Customize_Manager $customizeManager WordPress customize manager object
             */
            add_filter(
                'customize_save_after',
                wpHookProxy(static function (WP_Customize_Manager $customizeManager) use ($saveCustomizerData) {
                    $saveCustomizerData->updateCustomizerMenuData($customizeManager->changeset_data());
                })
            );
        });
    }

    /**
     * @inheritdoc
     * @param Container $container
     */
    public function bootstrap(Container $container)
    {
    }
}
