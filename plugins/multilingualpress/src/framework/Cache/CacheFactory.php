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

namespace Inpsyde\MultilingualPress\Framework\Cache;

use Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver;
use Inpsyde\MultilingualPress\Framework\Cache\Driver\EphemeralCacheDriver;
use Inpsyde\MultilingualPress\Framework\Cache\Driver\WpObjectCacheDriver;
use Inpsyde\MultilingualPress\Framework\Cache\Exception\InvalidCacheDriver;
use Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool;
use Inpsyde\MultilingualPress\Framework\Cache\Pool\WpCachePool;
use Inpsyde\MultilingualPress\Framework\Factory\ClassResolver;

/**
 * A factory for Cache pool objects.
 */
class CacheFactory
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var ClassResolver
     */
    private $classResolver;

    /**
     * @param string $prefix
     * @param string $poolDefaultClass
     */
    public function __construct(
        string $prefix = '',
        string $poolDefaultClass = WpCachePool::class
    ) {

        $this->prefix = $prefix;
        $this->classResolver = new ClassResolver(CachePool::class, $poolDefaultClass);
    }

    /**
     * @return string
     */
    public function prefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $namespace
     * @param CacheDriver|null $driver
     * @return CachePool
     */
    public function create(string $namespace, CacheDriver $driver = null): CachePool
    {
        $poolClass = $this->classResolver->resolve();

        return new $poolClass(
            $this->prefix() . $namespace,
            $driver ?? new WpObjectCacheDriver()
        );
    }

    /**
     * @param string $namespace
     * @param CacheDriver|null $driver
     * @return CachePool
     * @throws InvalidCacheDriver If a site-specific is used instead of a network one.
     */
    public function createForNetwork(string $namespace, CacheDriver $driver = null): CachePool
    {
        if ($driver && !$driver->isNetwork()) {
            throw InvalidCacheDriver::forSiteDriverAsNetwork($driver);
        }

        if (!$driver) {
            $driver = new WpObjectCacheDriver(CacheDriver::FOR_NETWORK);
        }

        $poolClass = $this->classResolver->resolve();

        return new $poolClass($this->prefix() . $namespace, $driver);
    }

    /**
     * @param string $namespace
     * @return CachePool
     */
    public function createEthereal(string $namespace): CachePool
    {
        $poolClass = $this->classResolver->resolve();

        return new $poolClass($this->prefix() . $namespace, new EphemeralCacheDriver());
    }

    /**
     * @param string $namespace
     * @return CachePool
     */
    public function createEtherealForNetwork(string $namespace): CachePool
    {
        $poolClass = $this->classResolver->resolve();

        return new $poolClass(
            $this->prefix() . $namespace,
            new EphemeralCacheDriver(CacheDriver::FOR_NETWORK)
        );
    }
}
