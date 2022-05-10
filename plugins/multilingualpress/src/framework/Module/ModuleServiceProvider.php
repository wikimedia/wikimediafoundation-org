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

namespace Inpsyde\MultilingualPress\Framework\Module;

use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider;

/**
 * Interface for all module service provider implementations to be used for dependency management.
 */
interface ModuleServiceProvider extends ServiceProvider
{
    /**
     * Registers the module at the module manager.
     *
     * @param ModuleManager $moduleManager
     * @return bool
     */
    public function registerModule(ModuleManager $moduleManager): bool;

    /**
     * Performs various tasks on module activation.
     *
     * @param Container $container
     */
    public function activateModule(Container $container);
}
