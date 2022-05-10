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

namespace Inpsyde\MultilingualPress\Framework\Cache\Server;

use Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;

class ItemLogic
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $key;

    /**
     * @var callable|null
     */
    private $updater;

    /**
     * @var int
     */
    private $timeToLive = 0;

    /**
     * @var int
     */
    private $extensionOnFailure = 0;

    /**
     * @var callable|null
     */
    private $keyGenerator;

    /**
     * @param string $namespace
     * @param string $key
     */
    public function __construct(string $namespace, string $key)
    {
        $this->namespace = $namespace;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function namespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return callable
     */
    public function updater(): callable
    {
        return $this->updater ?: '__return_null';
    }

    /**
     * @param array|null $args
     * @return callable
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function generateItemKey(...$args): string
    {
        if (!$this->keyGenerator) {
            return $args
                // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
                ? $this->key() . substr(md5(serialize($args)), -12, 10)
                : $this->key();
        }

        $key = ($this->keyGenerator)($this->key(), ...$args);
        if (is_string($key) && $key) {
            return $key;
        }

        return $this->key();
    }

    /**
     * @return int
     */
    public function timeToLive(): int
    {
        return $this->timeToLive > 1
            ? $this->timeToLive
            : CacheItem::LIFETIME_IN_SECONDS;
    }

    /**
     * @return int
     */
    public function extensionOnFailure(): int
    {
        return max($this->extensionOnFailure, 0);
    }

    /**
     * Set the callback that will be used to both generate and update the cache item value.
     *
     * The callback should throw an exception when the generation of the value fails.
     *
     * @param callable $callback
     * @return ItemLogic
     */
    public function updateWith(callable $callback): ItemLogic
    {
        // phpcs:enable

        $this->updater = $callback;

        return $this;
    }

    /**
     * Set the callback that will be used to generate an unique key for updater arguments.
     *
     * The callback will receive the "base" key as 1st argument, plus variadically all the updater
     * arguments, and has to return a string that's unique per key (likely should start with it)
     * and per arguments.
     *
     * Please note: the second argument passed to callback could be null.
     *
     * @see ItemLogic::generateItemKey()
     *
     * @param callable $callback
     * @return ItemLogic
     */
    public function generateKeyWith(callable $callback): ItemLogic
    {
        $this->keyGenerator = $callback;

        return $this;
    }

    /**
     * Set the time to live for the cached value.
     *
     * @param int $timeToLive
     * @return ItemLogic
     */
    public function liveFor(int $timeToLive): ItemLogic
    {
        $this->timeToLive = $timeToLive;

        return $this;
    }

    /**
     * @param int $extension
     * @return ItemLogic
     */
    public function onFailureExtendFor(int $extension): ItemLogic
    {
        $this->extensionOnFailure = $extension;

        return $this;
    }
}
