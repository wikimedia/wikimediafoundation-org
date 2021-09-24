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
 * Cache driver implementation that vanish with request.
 * Useful in tests or to share things that should never survive a single request
 * without polluting classes with many static variables.
 */
final class EphemeralCacheDriver implements CacheDriver
{

    const NOOP = 8192;

    /**
     * @var array
     */
    private static $cache = [];

    /**
     * @var bool
     */
    private $isNetwork;

    /**
     * @var bool
     */
    private $noop;

    /**
     * @param int $flags
     */
    public function __construct(int $flags = 0)
    {
        $this->isNetwork = (bool)($flags & self::FOR_NETWORK);
        $this->noop = (bool)($flags & self::NOOP);
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
        if ($this->noop) {
            return new Value();
        }

        $key = $this->buildKey($namespace, $name);

        return new Value(
            self::$cache[$key] ?? null,
            array_key_exists($key, self::$cache)
        );
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function write(string $namespace, string $name, $value): bool
    {
        // phpcs:enable

        if ($this->noop) {
            return false;
        }

        $key = $this->buildKey($namespace, $name);

        self::$cache[$key] = $value;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete(string $namespace, string $name): bool
    {
        if ($this->noop) {
            return false;
        }

        $key = $this->buildKey($namespace, $name);
        unset(self::$cache[$key]);

        return true;
    }

    /**
     * @param string $namespace
     * @param string $name
     * @return string
     */
    private function buildKey(string $namespace, string $name): string
    {
        $key = $namespace . $name;

        return $this->isNetwork
            ? "N_{$key}_"
            : 'S_' . get_current_blog_id() . "_{$key}_";
    }
}
