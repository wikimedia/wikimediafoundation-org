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

namespace Inpsyde\MultilingualPress\Framework\Cache\Pool;

use Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver;
use Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;
use Inpsyde\MultilingualPress\Framework\Cache\Item\WpCacheItem;

final class WpCachePool implements CachePool
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var CacheDriver
     */
    private $driver;

    /**
     * @var CacheItem[]
     */
    private $items = [];

    /**
     * @param string $namespace
     * @param CacheDriver $driver
     */
    public function __construct(string $namespace, CacheDriver $driver)
    {
        $this->namespace = $namespace;
        $this->driver = $driver;
    }

    /**
     * @inheritdoc
     */
    public function namespace(): string
    {
        $prefix = $this->isNetwork() ? 'n_' : '';

        return $prefix . $this->namespace;
    }

    /**
     * @inheritdoc
     */
    public function isNetwork(): bool
    {
        return $this->driver->isNetwork();
    }

    /**
     * @inheritdoc
     */
    public function item(string $key): CacheItem
    {
        if (!array_key_exists($key, $this->items)) {
            $this->items[$key] = new WpCacheItem(
                $this->driver,
                $key,
                $this->namespace()
            );
        }

        return $this->items[$key];
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function valueOfKey(string $key, $default = null)
    {
        // phpcs:enable

        $item = $this->item($key);

        if (!$item->isHit() || $item->isExpired()) {
            return $default;
        }

        return $item->value();
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function valuesOfKeys(array $keys, $default = null): array
    {
        // phpcs:enable

        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->valueOfKey($key, $default);
        }

        return $values;
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function cache(string $key, $value = null, int $ttl = null): CacheItem
    {
        // phpcs:enable

        $item = $this->item($key);
        if (null !== $ttl) {
            $item->liveFor($ttl);
        }
        $item->fillWith($value);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key): bool
    {
        if ($this->item($key)->delete()) {
            unset($this->items[$key]);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return $this->item($key)->isHit();
    }
}
