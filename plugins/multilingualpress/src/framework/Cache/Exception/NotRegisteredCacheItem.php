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

class NotRegisteredCacheItem extends Exception
{

    /**
     * @param string $namespace
     * @param string $key
     * @return NotRegisteredCacheItem
     */
    public static function forNamespaceAndKey(
        string $namespace,
        string $key
    ): NotRegisteredCacheItem {

        return new static(
            sprintf(
                'The namespace/key pair "%s"/"%s" does not belong to any registered cache logic in cache server.',
                $namespace,
                $key
            )
        );
    }
}
