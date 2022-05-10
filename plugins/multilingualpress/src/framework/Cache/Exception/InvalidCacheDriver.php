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

namespace Inpsyde\MultilingualPress\Framework\Cache\Exception;

use Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver;

class InvalidCacheDriver extends Exception
{

    const SITE_DRIVER_AS_NETWORK = 1;

    /**
     * @param CacheDriver $driver
     * @return InvalidCacheDriver
     */
    public static function forSiteDriverAsNetwork(
        CacheDriver $driver
    ): InvalidCacheDriver {

        $type = get_class($driver);

        return new static(
            "Cannot create a network-wide cache with driver of type \"{$type}\" which is not for network.",
            self::SITE_DRIVER_AS_NETWORK
        );
    }
}
