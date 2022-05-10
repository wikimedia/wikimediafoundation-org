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

/**
 * A driver implementation that uses WordPress transient functions.
 *
 * Two gotchas:
 * 1) when using external object cache, prefer object cache driver, WP will use object cache anyway;
 * 2) avoid to store `false` using this driver because it can't be disguised from a cache miss.
 */
final class WpTransientDriver implements CacheDriver
{

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
        $key = $this->buildKey($namespace, $name);

        /*
         * When using external object cache, WP uses it for transient anyway.
         * Using cache methods directly helps to disguise the $found value.
         */
        if (wp_using_ext_object_cache()) {
            $group = $this->isNetwork ? 'site-transient' : 'transient';
            $found = false;
            $value = wp_cache_get($key, $group, true, $found);

            return new Value($value, $found);
        }

        $value = $this->isNetwork
            ? get_site_transient($key)
            : get_transient($key);

        /*
         * Transient do not allow to lookup for "hit" or "miss", so there's not
         * way to know if a `false` was returned because cached or because value
         * not found.
         * For consistence with other drivers we return null as value when
         * transient functions returns false.
         * It means that a cached `null` value can be disguised, but not a
         * cached `false`.
         * Avoid to use store `false` in cache when using this driver.
         */
        $found = false !== $value;

        return new Value($found ? $value : null, $found);
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function write(string $namespace, string $name, $value): bool
    {
        // phpcs:enable

        return $this->isNetwork
            ? set_site_transient($this->buildKey($namespace, $name), $value, 0)
            : set_transient($this->buildKey($namespace, $name), $value, 0);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $namespace, string $name): bool
    {
        return $this->isNetwork
            ? delete_site_transient($this->buildKey($namespace, $name))
            : delete_transient($this->buildKey($namespace, $name));
    }

    /**
     * Site transients limits key to 40 characters or less.
     * This method builds a key that is unique per namespace and key and is 39
     * characters or less.
     *
     * @param string $namespace
     * @param string $name
     * @return string
     */
    private function buildKey(string $namespace, string $name): string
    {
        $full = $namespace . $name;
        $prefix = substr($full, 0, 7);

        return $prefix . md5($full);
    }
}
