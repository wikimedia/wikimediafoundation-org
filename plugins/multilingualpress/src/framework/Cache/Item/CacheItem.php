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

namespace Inpsyde\MultilingualPress\Framework\Cache\Item;

interface CacheItem
{

    const LIFETIME_IN_SECONDS = HOUR_IN_SECONDS;

    /**
     * Cache item key.
     *
     * @return string
     */
    public function key(): string;

    /**
     * Cache item value.
     *
     * @return mixed
     */
    public function value();

    /**
     * Check if the cache item was a hit. Necessary to disguise null values stored in cache.
     *
     * @return bool
     */
    public function isHit(): bool;

    /**
     * Check if the cache item is expired.
     *
     * @return bool
     */
    public function isExpired(): bool;

    /**
     * Sets the value for the cache item.
     *
     * @param mixed $value
     * @return bool
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function fillWith($value): bool;

    /**
     * Delete the cache item from its storage and ensure that next value() call return null.
     *
     * @return bool
     *
     * phpcs:enable
     */
    public function delete(): bool;

    /**
     * Sets a specific time to live for the item.
     *
     * @param int $ttl
     * @return CacheItem
     */
    public function liveFor(int $ttl): CacheItem;

    /**
     * Push values to storage driver.
     *
     * @return bool
     */
    public function syncToStorage(): bool;

    /**
     * Load values from storage driver.
     *
     * @return bool
     */
    public function syncFromStorage(): bool;
}
