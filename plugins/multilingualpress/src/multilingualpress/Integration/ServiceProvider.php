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

namespace Inpsyde\MultilingualPress\Integration;

use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;

/**
 * Service provider for all third-party integrations.
 */
final class ServiceProvider implements IntegrationServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container[WpCli::class] = static function (): WpCli {
            return new WpCli();
        };
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        $container[WpCli::class]->integrate();
    }
}
