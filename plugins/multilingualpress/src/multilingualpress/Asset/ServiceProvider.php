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

namespace Inpsyde\MultilingualPress\Asset;

use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;

/**
 * Service provider for all assets objects.
 */
final class ServiceProvider implements BootstrappableServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container->share(
            AssetFactory::class,
            static function (Container $container): AssetFactory {
                return new AssetFactory($container[Locations::class]);
            }
        );

        $container->share(
            AssetManager::class,
            static function (): AssetManager {
                return new AssetManager();
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function bootstrap(Container $container)
    {
        $assetFactory = $container[AssetFactory::class];

        $container[AssetManager::class]
            ->registerStyle(
                $assetFactory->createInternalStyle(
                    'multilingualpress-admin',
                    'admin.min.css'
                )
            )
            ->registerScript(
                $assetFactory->createInternalScript(
                    'multilingualpress-admin',
                    'admin.min.js',
                    ['jquery-ui-tabs', 'jquery-ui-autocomplete', 'underscore']
                )
            );
    }
}
