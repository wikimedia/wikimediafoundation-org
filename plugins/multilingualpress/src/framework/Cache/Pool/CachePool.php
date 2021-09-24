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

namespace Inpsyde\MultilingualPress\Framework\Cache\Pool;

use Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;

/**
 * Interface for all cache pool implementations.
 *
 * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
 */
interface CachePool
{
    /**
     * Return pool namespace.
     *
     * @return string
     */
    public function namespace(): string;

    /**
     * Check if the cache pool is for network.
     *
     * @return bool
     */
    public function isNetwork(): bool;

    /**
     * Fetches a value from the cache.
     *
     * @param string $key
     * @return CacheItem
     */
    public function item(string $key): CacheItem;

    /**
     * Fetches a value from the cache.
     *
     * The difference between `$pool->get($key)` and `$pool->item($key)->value()` is that the latter
     * could return a cached value even if expired, and it is responsibility of the caller check for
     * that if necessary. Moreover, `get()` also has a default param that is returned in case cache
     * is a miss or is expired.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function valueOfKey(string $key, $default = null);

    /**
     * Fetches a value from the cache.
     *
     * The "bulk" version of `get()`.
     *
     * @param string[] $keys
     * @param mixed|null $default
     * @return array
     */
    public function valuesOfKeys(array $keys, $default = null): array;

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key
     * @param mixed $value
     * @param null|int $ttl
     * @return CacheItem
     */
    public function cache(string $key, $value = null, int $ttl = null): CacheItem;

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * Determines whether an item is present in the cache.
     *
     * A true outcome does not provide warranty the value is not expired.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;
}
