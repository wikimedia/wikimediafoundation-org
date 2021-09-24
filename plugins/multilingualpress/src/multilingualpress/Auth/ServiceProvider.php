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

namespace Inpsyde\MultilingualPress\Auth;

use Inpsyde\MultilingualPress\Framework\Auth\AuthFactory;
use Inpsyde\MultilingualPress\Framework\Auth\EntityAuthFactory;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider as FrameworkServiceProvider;

/**
 * Class ServiceProvider
 * @package Inpsyde\MultilingualPress\Auth
 */
class ServiceProvider implements FrameworkServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register(Container $container)
    {
        $container->addService(
            AuthFactory::class,
            static function (): AuthFactory {
                return new AuthFactory();
            }
        );

        $container->addService(
            EntityAuthFactory::class,
            static function (): EntityAuthFactory {
                return new EntityAuthFactory();
            }
        );
    }
}
