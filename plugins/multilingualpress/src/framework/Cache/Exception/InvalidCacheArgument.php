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

class InvalidCacheArgument extends Exception
{

    /**
     * @param string $namespace
     * @param string $key
     * @return InvalidCacheArgument
     */
    public static function forNamespaceAndKey(
        string $namespace,
        string $key
    ): InvalidCacheArgument {

        return new static(
            sprintf(
                'The arguments for the cache updater callback of namespace/key pair "%s"/"%s", ' .
                'are not valid because contain variables of type "object" or "resource".',
                $namespace,
                $key
            )
        );
    }
}
