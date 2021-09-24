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

namespace Inpsyde\MultilingualPress\Module\QuickLinks;

use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Core\Admin\PluginSettingsUpdater;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\Core\ServiceProvider as CoreServiceProvider;
use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTab;
use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\Translations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed;
use Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer;
use Inpsyde\MultilingualPress\Module\QuickLinks\Model\CollectionFactory;
use Inpsyde\MultilingualPress\Module\QuickLinks\Settings\QuickLinksPositionViewModel;
use Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository;
use Inpsyde\MultilingualPress\Module\QuickLinks\Settings\TabView;
use wpdb;

use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Class ServiceProvider
 * @package Inpsyde\MultilingualPress\Module\QuickLinks
 */
class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'quick_links';
    const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'quicklinks_assets_factory';

    /**
     * @inheritDoc
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     * @param Container $container
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $container->addService(
            ValidateRedirectFilter::class,
            static function (Container $container): ValidateRedirectFilter {
                return new ValidateRedirectFilter(
                    $container[wpdb::class]
                );
            }
        );

        $container->addService(
            Redirector::class,
            static function (Container $container): Redirector {
                return new Redirector(
                    $container[NonceFactory::class]->create(['quicklinks_redirector'])
                );
            }
        );

        $container->addService(
            CollectionFactory::class,
            static function (Container $container): CollectionFactory {
                return new CollectionFactory(
                    $container[ContentRelations::class],
                    $container[SiteSettingsRepository::class],
                    $container[Translations::class]
                );
            }
        );

        $container->addService(
            QuickLink::class,
            static function (Container $container): QuickLink {
                return new QuickLink(
                    $container[CollectionFactory::class],
                    $container[NonceFactory::class]->create(['quicklinks_redirector']),
                    $container[Repository::class]
                );
            }
        );

        /* ----------------------------------------------------
           Settings
           ------------------------------------------------- */

        $container->addService(
            Repository::class,
            static function (): Repository {
                return new Repository();
            }
        );

        $container->addService(
            Settings\Updater::class,
            static function (Container $container): Settings\Updater {
                return new Settings\Updater(
                    $container[NonceFactory::class]->create(['save_module_quicklinks_settings']),
                    $container[Repository::class]
                );
            }
        );

        $container->share(
            self::MODULE_ASSETS_FACTORY_SERVICE_NAME,
            static function (Container $container): AssetFactory {
                $pluginProperties = $container[PluginProperties::class];

                $locations = new Locations();
                $locations
                    ->add(
                        'css',
                        $pluginProperties->dirPath() . 'src/modules/QuickLinks/public/css',
                        $pluginProperties->dirUrl() . 'src/modules/QuickLinks/public/css'
                    )
                    ->add(
                        'js',
                        $pluginProperties->dirPath() . 'src/modules/QuickLinks/public/js',
                        $pluginProperties->dirUrl() . 'src/modules/QuickLinks/public/js'
                    );

                return new AssetFactory($locations);
            }
        );

        add_filter(
            CoreServiceProvider::ACTION_BUILD_TABS,
            static function (array $tabs) use ($container) {

                $settingsRepository = $container[Repository::class];
                $nonceFactory = $container[NonceFactory::class];
                $moduleManager = $container[ModuleManager::class];

                if ($moduleManager->isModuleActive(self::MODULE_ID)) {
                    $tabs['quicklinks'] = new SettingsPageTab(
                        new SettingsPageTabData(
                            'quicklinks',
                            esc_html_x(
                                'QuickLinks',
                                'QuickLinks Module Settings',
                                'multilingualpress'
                            ),
                            'quicklinks'
                        ),
                        new TabView(
                            $nonceFactory->create(['save_module_quicklinks_settings']),
                            new QuickLinksPositionViewModel($settingsRepository)
                        )
                    );
                }

                return $tabs;
            }
        );
    }

    /**
     * @inheritDoc
     * @param ModuleManager $moduleManager
     * @return bool
     * @throws ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => __(
                        'Show link to translations in post content.',
                        'multilingualpress'
                    ),
                    'name' => __('QuickLink', 'multilingualpress'),
                    'active' => false,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     * @throws AssetException
     */
    public function activateModule(Container $container)
    {
        is_admin()
            ? $this->activateModuleForAdmin($container)
            : $this->activateModuleForFrontend($container);
    }

    /**
     * Activate Module for Admin
     *
     * @param Container $container
     * @throws AssetException
     */
    protected function activateModuleForAdmin(Container $container)
    {
        $this->setupScriptsForAdmin($container);

        add_action(
            PluginSettingsUpdater::ACTION_UPDATE_PLUGIN_SETTINGS,
            [$container[Settings\Updater::class], 'updateSettings']
        );
    }

    /**
     * Register and Enqueue Scripts for Admin
     *
     * @param Container $container
     * @throws AssetException
     */
    protected function setupScriptsForAdmin(Container $container)
    {
        $assetFactory = $container[self::MODULE_ASSETS_FACTORY_SERVICE_NAME];
        $assetManager = $container[AssetManager::class];

        $adminStyle = $assetFactory->createInternalStyle(
            'multilingualpress-quicklinks-admin',
            'admin.min.css',
            [],
            null,
            'screen'
        );

        try {
            $assetManager
                ->registerStyle($adminStyle)
                ->enqueueStyle('multilingualpress-quicklinks-admin');
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }
    }

    /**
     * Activate module for Frontend
     *
     * @param Container $container
     * @throws AssetException
     */
    protected function activateModuleForFrontend(Container $container)
    {
        add_filter('the_content', [$container[QuickLink::class], 'filter']);
        add_action('wp_loaded', [$container[Redirector::class], 'redirect']);

        $this->setupValidateRedirectFilter($container);
        $this->setupScriptsForFrontend($container);
    }

    /**
     * Register and Enqueue Scripts for Frontend
     *
     * @param Container $container
     * @throws AssetException
     */
    protected function setupScriptsForFrontend(Container $container)
    {
        $assetFactory = $container[self::MODULE_ASSETS_FACTORY_SERVICE_NAME];
        $assetManager = $container[AssetManager::class];

        $frontendStyle = $assetFactory->createInternalStyle(
            'multilingualpress-quicklinks-front',
            'frontend.min.css',
            [],
            null,
            'screen'
        );
        $frontendScript = $assetFactory->createInternalScript(
            'multilingualpress-quicklinks-front',
            'frontend.min.js'
        );

        $assetManager
            ->registerStyle($frontendStyle)
            ->registerScript($frontendScript);

        try {
            $assetManager
                ->enqueueStyle('multilingualpress-quicklinks-front')
                ->enqueueScript('multilingualpress-quicklinks-front');
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }
    }

    /**
     * Setup ValidateRedirectFilter
     */
    protected function setupValidateRedirectFilter(Container $container)
    {
        $filter = $container[ValidateRedirectFilter::class];
        $filter->enable();
        add_action(Redirector::ACTION_AFTER_VALIDATE_REDIRECT, [$filter, 'disable']);
    }
}
