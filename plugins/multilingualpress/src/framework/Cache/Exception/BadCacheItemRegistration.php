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

class BadCacheItemRegistration extends Exception
{

    /**
     * @return BadCacheItemRegistration
     */
    public static function forWrongTiming(): BadCacheItemRegistration
    {
        return new static(
            'Cache item logic registration is not possible during cache update requests.'
        );
    }

    /**
     * @param string $key
     * @return BadCacheItemRegistration
     */
    public static function forKeyUsedForNetwork(string $key): BadCacheItemRegistration
    {
        return new static(
            sprintf(
                "Cache key '%s' is used for network-wide items, can't be used for site-wide items.",
                $key
            )
        );
    }

    /**
     * @param string $key
     * @return BadCacheItemRegistration
     */
    public static function forKeyUsedForSite(string $key): BadCacheItemRegistration
    {
        return new static(
            sprintf(
                "Cache key '%s' is used for site-wide items, can't be used for network-wide items.",
                $key
            )
        );
    }
}
