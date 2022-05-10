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

namespace Inpsyde\MultilingualPress\Framework\Cache\Driver;

use Inpsyde\MultilingualPress\Framework\Cache\Item\Value;

final class WpObjectCacheDriver implements CacheDriver
{

    /**
     * @var string[]
     */
    private static $globalNamespaces = [];

    /**
     * @var bool
     */
    private $isNetwork;

    /**
     * @param int $flags
     */
    public function __construct(int $flags = 0)
    {
        $this->isNetwork = (bool)($flags & self::FOR_NETWORK);
    }

    /**
     * @inheritdoc
     */
    public function isNetwork(): bool
    {
        return $this->isNetwork;
    }

    /**
     * @inheritdoc
     */
    public function read(string $namespace, string $name): Value
    {
        $this->maybeGlobal($namespace);
        $found = false;
        $value = wp_cache_get($name, $namespace, true, $found);
        if (false === $value && !$found) {
            $value = null;
        }

        return new Value($value, $found);
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function write(string $namespace, string $name, $value): bool
    {
        // phpcs:enable

        $this->maybeGlobal($namespace);

        return (bool)wp_cache_set($name, $value, $namespace, 0);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $namespace, string $name): bool
    {
        $this->maybeGlobal($namespace);

        return (bool)wp_cache_delete($name, $namespace);
    }

    /**
     * @param string $namespace
     */
    private function maybeGlobal(string $namespace)
    {
        if (
            $this->isNetwork
            && !in_array($namespace, self::$globalNamespaces, true)
        ) {
            self::$globalNamespaces[] = $namespace;
            wp_cache_add_global_groups($namespace);
        }
    }
}
