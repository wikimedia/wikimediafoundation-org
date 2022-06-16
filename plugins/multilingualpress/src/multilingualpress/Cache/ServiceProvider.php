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

namespace Inpsyde\MultilingualPress\Cache;

use Inpsyde\MultilingualPress\Core\Admin\PluginSettingsUpdater;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingNamesValidator;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptionsView;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsTabView;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsUpdater;
use Inpsyde\MultilingualPress\Core\ServiceProvider as CoreServiceProvider;
use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTab;
use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData;
use Inpsyde\MultilingualPress\Framework\Auth\RequestAuth;
use Inpsyde\MultilingualPress\Framework\Auth\WpUserCapability;
use Inpsyde\MultilingualPress\Framework\Cache\CacheFactory;
use Inpsyde\MultilingualPress\Framework\Cache\Driver\WpObjectCacheDriver;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Server;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed;
use Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer;

/**
 * Service provider for all cache objects.
 */
final class ServiceProvider implements BootstrappableServiceProvider
{
    const CACHE_SETTINGS_NONCE = 'update_internal_cache_settings';

    /**
     * @inheritdoc
     * @param Container $container
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     */
    public function register(Container $container)
    {
        $container->share(
            CacheFactory::class,
            static function (Container $container): CacheFactory {
                $version = $container[PluginProperties::class]->version();

                return new CacheFactory("mlp_{$version}_");
            }
        );

        $container->share(
            Server::class,
            static function (Container $container): Server {
                return new Server(
                    $container[CacheFactory::class],
                    new WpObjectCacheDriver(),
                    new WpObjectCacheDriver(WpObjectCacheDriver::FOR_NETWORK),
                    $container[ServerRequest::class]
                );
            }
        );

        $this->registerSettings($container);
    }

    /**
     * @param Container $container
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     */
    private function registerSettings(Container $container)
    {
        $container->addService(
            CacheSettingNamesValidator::class,
            static function (): CacheSettingNamesValidator {
                return new CacheSettingNamesValidator();
            }
        );

        $container->addService(
            CacheSettingsOptions::class,
            static function (): CacheSettingsOptions {
                return new CacheSettingsOptions();
            }
        );

        $container->addService(
            CacheSettingsRepository::class,
            static function (Container $container): CacheSettingsRepository {
                return new CacheSettingsRepository(
                    $container[CacheSettingsOptions::class],
                    $container[CacheSettingNamesValidator::class]
                );
            }
        );

        $container->addService(
            'cache.RequestAuth',
            static function (Container $container): RequestAuth {
                return new RequestAuth(
                    $container[NonceFactory::class]->create([self::CACHE_SETTINGS_NONCE]),
                    new WpUserCapability(wp_get_current_user(), 'manage_network_options', 0)
                );
            }
        );

        $container->addService(
            CacheSettingsUpdater::class,
            static function (Container $container): CacheSettingsUpdater {
                return new CacheSettingsUpdater(
                    $container[CacheSettingsRepository::class],
                    $container['cache.RequestAuth'],
                    $container[CacheSettingsOptions::class]
                );
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function bootstrap(Container $container)
    {
        $container[Server::class]->listenSpawn();

        is_admin() and $this->bootstrapNetworkAdmin($container);
    }

    /**
     * @param Container $container
     */
    public function bootstrapNetworkAdmin(Container $container)
    {
        add_filter(
            CoreServiceProvider::ACTION_BUILD_TABS,
            static function (array $tabs) use ($container) {

                $tabs['internal-cache'] = new SettingsPageTab(
                    new SettingsPageTabData(
                        'internal-cache',
                        __('Cache', 'multilingualpress'),
                        'internal-cache'
                    ),
                    new CacheSettingsTabView(
                        new CacheSettingsOptionsView(
                            $container[CacheSettingsRepository::class],
                            $container[CacheSettingsOptions::class]
                        ),
                        $container[NonceFactory::class]->create([self::CACHE_SETTINGS_NONCE])
                    )
                );

                return $tabs;
            }
        );

        add_action(
            PluginSettingsUpdater::ACTION_UPDATE_PLUGIN_SETTINGS,
            [$container[CacheSettingsUpdater::class], 'updateSettings']
        );
    }
}
