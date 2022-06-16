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

namespace Inpsyde\MultilingualPress\License;

use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\License\Api\Activator;
use Inpsyde\MultilingualPress\License\Api\Updater;

use function Inpsyde\MultilingualPress\wpHookProxy;

use const Inpsyde\MultilingualPress\MULTILINGUALPRESS_LICENSE_API_URL;

class ServiceProvider implements BootstrappableServiceProvider
{
    public function register(Container $container)
    {
        $container->addService(
            Updater::class,
            static function (Container $container): Updater {
                $pluginProperties = $container[PluginProperties::class];
                $licenseOption = get_network_option(0, 'multilingualpress_license', []);
                $licenseProductId = isset($licenseOption['license_product_id'])
                    ? $licenseOption['license_product_id'] : '';
                $apiKey = isset($licenseOption['api_key']) ? $licenseOption['api_key'] : '';
                $instanceKey = isset($licenseOption['instance_key']) ? $licenseOption['instance_key'] : '';
                $status = isset($licenseOption['status']) ? $licenseOption['status'] : '';
                $license = new License($licenseProductId, $apiKey, $instanceKey, $status);

                return new Updater(
                    [
                        'basename' => $pluginProperties->basename(),
                        'version' => $pluginProperties->version(),
                        'slug' => $pluginProperties->textDomain(),
                    ],
                    [
                        'product_id' => 'MultilingualPress+3',
                        'license_api_url' => MULTILINGUALPRESS_LICENSE_API_URL,
                    ],
                    $license
                );
            }
        );

        $container->addService(
            Activator::class,
            static function (Container $container): Activator {
                $pluginProperties = $container[PluginProperties::class];

                return new Activator(
                    [
                        'license_api_url' => MULTILINGUALPRESS_LICENSE_API_URL,
                        'version' => $pluginProperties->version(),
                    ]
                );
            }
        );
    }

    public function bootstrap(Container $container)
    {
        $licenseUpdater = $container[Updater::class];
        add_filter(
            'pre_set_site_transient_update_plugins',
            wpHookProxy([$licenseUpdater, 'updateCheck'])
        );

        add_filter(
            'plugins_api',
            wpHookProxy([$licenseUpdater, 'pluginInformation']),
            10,
            3
        );
    }
}
