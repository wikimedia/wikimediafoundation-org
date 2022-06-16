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

namespace Inpsyde\MultilingualPress\Framework\Cache\Driver;

use Inpsyde\MultilingualPress\Framework\Cache\Item\Value;

interface CacheDriver
{

    const FOR_NETWORK = 32;

    /**
     * @return bool
     */
    public function isNetwork(): bool;

    /**
     * Reads a value from the cache.
     *
     * @param string $namespace
     * @param string $name
     * @return Value
     */
    public function read(string $namespace, string $name): Value;

    /**
     * Write a value to the cache.
     *
     * @param string $namespace
     * @param string $name
     * @param mixed $value
     * @return bool
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function write(string $namespace, string $name, $value): bool;

    /**
     * Delete a value from the cache.
     *
     * @param string $namespace
     * @param string $name
     * @return bool
     *
     * phpcs:enable
     */
    public function delete(string $namespace, string $name): bool;
}
