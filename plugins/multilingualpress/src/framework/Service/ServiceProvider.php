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
 * Interface for all service provider implementations to be used for dependency management.
 */
interface ServiceProvider
{

    /**
     * Registers the provided services on the given container.
     *
     * @param Container $container
     */
    public function register(Container $container);
}
