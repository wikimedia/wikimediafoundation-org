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

namespace Inpsyde\MultilingualPress;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Installation\InstallationChecker;
use Inpsyde\MultilingualPress\Installation\SystemChecker;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection;

/**
 * MultilingualPress front controller.
 */
final class MultilingualPress
{
    const ACTION_BOOTSTRAPPED = 'multilingualpress.bootstrapped';
    const ACTION_REGISTER_MODULES = 'multilingualpress.register_modules';
    const OPTION_VERSION = 'multilingualpress_version';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var ServiceProvidersCollection
     */
    private $serviceProviders;

    /**
     * @param Container $container
     * @param ServiceProvidersCollection $serviceProviders
     */
    public function __construct(Container $container, ServiceProvidersCollection $serviceProviders)
    {
        $this->container = $container;
        $this->serviceProviders = $serviceProviders;
    }

    /**
     * Bootstraps MultilingualPress.
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function bootstrap(): bool
    {
        if (did_action(self::ACTION_BOOTSTRAPPED)) {
            throw new \RuntimeException(
                'Cannot bootstrap an instance that has already been bootstrapped.'
            );
        }

        $this->serviceProviders->applyMethod('register', $this->container);

        // Lock the container. Nothing can be registered after that.
        $this->container->lock();

        $integrations = $this->serviceProviders->filter(
            static function (ServiceProvider $provider): bool {
                return $provider instanceof IntegrationServiceProvider;
            }
        );
        $integrations->applyMethod('integrate', $this->container);

        if (!$this->isPluginActivated()) {
            return false;
        }

        $bootstrappable = $this->serviceProviders->filter(
            static function (ServiceProvider $provider): bool {
                return $provider instanceof BootstrappableServiceProvider;
            }
        );

        $bootstrappable->applyMethod('bootstrap', $this->container);

        $this->needsModules() and $this->registerModules();

        $this->container->bootstrap();

        /**
         * Fires right after MultilingualPress was bootstrapped.
         */
        do_action(static::ACTION_BOOTSTRAPPED);

        return true;
    }

    /**
     * @return bool
     */
    private function isPluginActivated(): bool
    {
        $installationStatus = $this->container[InstallationChecker::class]->check();

        return $installationStatus !== SystemChecker::PLUGIN_DEACTIVATED;
    }

    /**
     * Checks if the current request needs MultilingualPress to register any modules.
     *
     * @return bool
     */
    private function needsModules(): bool
    {
        if (
            is_network_admin()
            || in_array($GLOBALS['pagenow'], ['admin-ajax.php', 'admin-post.php'], true)
        ) {
            return true;
        }

        return in_array(
            get_current_blog_id(),
            $this->container[SiteSettingsRepository::class]->allSiteIds(),
            true
        );
    }

    /**
     * Registers all modules.
     */
    private function registerModules()
    {
        /**
         * Fires right before MultilingualPress registers any modules.
         */
        do_action(static::ACTION_REGISTER_MODULES);

        $activation = static function (
            ModuleServiceProvider $module,
            ModuleManager $moduleManager,
            Container $container
        ) {
            if ($module->registerModule($moduleManager)) {
                $module->activateModule($container);
            }
        };

        $moduleProviders = $this->serviceProviders->filter(
            static function (ServiceProvider $provider): bool {
                return $provider instanceof ModuleServiceProvider;
            }
        );

        $moduleProviders->applyCallback(
            $activation,
            $this->container[ModuleManager::class],
            $this->container
        );
    }
}
