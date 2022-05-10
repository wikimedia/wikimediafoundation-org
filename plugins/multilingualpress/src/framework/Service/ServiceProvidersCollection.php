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

namespace Inpsyde\MultilingualPress\Framework\Service;

/**
 * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
 */
class ServiceProvidersCollection implements \Countable
{
    /**
     * @var \SplObjectStorage
     */
    private $storage;

    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }

    /**
     * Adds the given service provider to the collection.
     *
     * @param ServiceProvider $provider
     * @return ServiceProvidersCollection
     */
    public function add(ServiceProvider $provider): ServiceProvidersCollection
    {
        $this->storage->attach($provider);

        return $this;
    }

    /**
     * Removes the given service provider from the collection.
     *
     * @param ServiceProvider $provider
     * @return ServiceProvidersCollection
     */
    public function remove(ServiceProvider $provider): ServiceProvidersCollection
    {
        $this->storage->detach($provider);

        return $this;
    }

    /**
     * Calls the method with the given name on all registered providers,
     * and passes on potential further arguments.
     *
     * @param string $methodName
     * @param array ...$args
     */
    public function applyMethod(string $methodName, ...$args)
    {
        $this->storage->rewind();

        while ($this->storage->valid()) {
            /** @var callable $method */
            $method = [$this->storage->current(), $methodName];
            $method(...$args);

            $this->storage->next();
        }
    }

    /**
     * Executes the given callback for all registered providers,
     * and passes along potential further arguments.
     *
     * @param callable $callback
     * @param array ...$args
     */
    public function applyCallback(callable $callback, ...$args)
    {
        $this->storage->rewind();

        while ($this->storage->valid()) {
            $callback($this->storage->current(), ...$args);
            $this->storage->next();
        }
    }

    /**
     * Executes the given callback for all registered providers, and returns the instance that
     * contains the providers that passed the filtering.
     *
     * @param callable $callback
     * @param array ...$args
     * @return ServiceProvidersCollection
     */
    public function filter(callable $callback, ...$args): ServiceProvidersCollection
    {
        $collection = new static();

        $this->storage->rewind();
        while ($this->storage->valid()) {
            /** @var ServiceProvider $provider */
            $provider = $this->storage->current();
            if ($callback($provider, ...$args)) {
                $collection->add($provider);
            }

            $this->storage->next();
        }

        return $collection;
    }

    /**
     * Executes the given callback for all registered providers, and returns the instance that
     * contains the providers obtained.
     *
     * @param callable $callback
     * @param array ...$args
     * @return ServiceProvidersCollection
     * @throws \UnexpectedValueException If a given callback did not return a service provider instance.
     */
    public function map(callable $callback, ...$args): ServiceProvidersCollection
    {
        $collection = new static();

        $this->storage->rewind();
        while ($this->storage->valid()) {
            $provider = $callback($this->storage->current(), ...$args);
            if (!$provider instanceof ServiceProvider) {
                throw new \UnexpectedValueException(
                    __METHOD__ . ' expects transformation callbacks to return a service provider instance.'
                );
            }

            $collection->add($provider);
            $this->storage->next();
        }

        return $collection;
    }

    /**
     * Executes the given callback for all registered providers, and passes along the result of
     * previous callback.
     *
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function reduce(callable $callback, $initial = null)
    {
        // phpcs:enable

        $this->storage->rewind();
        $carry = $initial;
        while ($this->storage->valid()) {
            $carry = $callback($carry, $this->storage->current());
            $this->storage->next();
        }

        return $carry;
    }

    /**
     * Returns the number of providers in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->storage->count();
    }
}
