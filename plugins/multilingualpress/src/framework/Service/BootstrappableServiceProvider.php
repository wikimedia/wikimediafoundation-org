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

namespace Inpsyde\MultilingualPress\Framework\Service;

/**
 * Interface for all bootstrappable service provider implementations.
 */
interface BootstrappableServiceProvider extends ServiceProvider
{
    /**
     * Bootstraps the registered services.
     *
     * @param Container $container
     */
    public function bootstrap(Container $container);
}
