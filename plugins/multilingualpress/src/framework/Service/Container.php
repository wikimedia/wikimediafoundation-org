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
 * Append-only container implementation to be used for dependency management.
 */
class Container implements \ArrayAccess
{
    /**
     * @var callable[]
     */
    private $factories = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var string[]
     */
    private $shared = [];

    /**
     * @var string[]
     */
    private $factoryOnly = [];

    /**
     * @var bool
     */
    private $isBootstrapped = false;

    /**
     * @var bool
     */
    private $isLocked = false;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $name => $value) {
            $this->offsetSet($name, $value);
        }
    }

    /**
     * Bootstraps (and locks) the container.
     *
     * Only shared values and factory callbacks are accessible from now on.
     */
    public function bootstrap()
    {
        $this->lock();
        $this->isBootstrapped = true;
    }

    /**
     * Locks the container.
     *
     * A locked container cannot be manipulated anymore.
     * All stored values and factory callbacks are still accessible.
     */
    public function lock()
    {
        $this->isLocked = true;
    }

    /**
     * Returns true when either a service factory callback or a value with the given name are stored
     * stored in the container.
     *
     * PSR-11 compatible.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->values) || array_key_exists($name, $this->factories);
    }

    /**
     * Retrieve a service or a value from the container.
     *
     * Values are just returned.
     * Services are built first time they are requested.
     * Stored factories are always executed and the returned value is returned.
     *
     * PSR-11 compatible.
     *
     * @param string $name
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function get(string $name)
    {
        // phpcs:enable

        if (!$this->has($name)) {
            throw Exception\NameNotFound::forName($name);
        }

        if ($this->isBootstrapped && !in_array($name, $this->shared, true)) {
            throw Exception\LateAccessToNotSharedService::forService($name, 'read');
        }

        if (in_array($name, $this->factoryOnly, true)) {
            return $this->factories[$name]($this);
        }

        if (!array_key_exists($name, $this->values)) {
            $this->values[$name] = $this->factories[$name]($this);
            if ($this->isLocked) {
                unset($this->factories[$name]);
            }
        }

        return $this->values[$name];
    }

    /**
     * Stores the given service factory callback with the given name.
     *
     * @param string $name
     * @param callable $factory
     * @return Container
     * @throws Exception\NameOverwriteNotAllowed
     * @throws Exception\WriteAccessOnLockedContainer
     */
    public function addService(string $name, callable $factory): Container
    {
        if ($this->isLocked) {
            throw Exception\WriteAccessOnLockedContainer::forName($name);
        }

        if (array_key_exists($name, $this->values)) {
            throw Exception\NameOverwriteNotAllowed::forServiceName($name);
        }

        $this->factories[$name] = $factory;

        return $this;
    }

    /**
     * Stores the given value with the given name.
     *
     * Scalar values are automatically shared.
     *
     * @param string $name
     * @param $value
     * @return Container
     * @throws Exception\NameOverwriteNotAllowed
     * @throws Exception\WriteAccessOnLockedContainer
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function addValue(string $name, $value): Container
    {
        // phpcs:enable

        if ($this->isLocked) {
            throw Exception\WriteAccessOnLockedContainer::forName($name);
        }

        if (array_key_exists($name, $this->values)) {
            throw Exception\NameOverwriteNotAllowed::forValueName($name);
        }

        $this->values[$name] = $value;

        if (is_scalar($value) && !in_array($name, $this->shared, true)) {
            $this->shared[] = $name;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param callable $factory
     * @return Container
     */
    public function addFactory(string $name, callable $factory): Container
    {
        $this->addService($name, $factory);
        $this->factoryOnly[] = $name;

        return $this;
    }

    /**
     * Stores the given value or factory callback with the given name, and defines it to be
     * accessible even after the container has been bootstrapped.
     *
     * @param string $name
     * @param callable $factory
     * @return Container
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function share(string $name, callable $factory): Container
    {
        // phpcs:enable

        $this->addService($name, $factory);
        $this->shared[] = $name;

        return $this;
    }

    /**
     * Stores the given value with the given name, and defines it to be
     * accessible even after the container has been bootstrapped.
     *
     * Scalar values are automatically shared.
     *
     * @param string $name
     * @param $value
     * @return Container
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function shareValue(string $name, $value): Container
    {
        // phpcs:enable

        $this->addValue($name, $value);
        $this->shared[] = $name;

        return $this;
    }

    /**
     * @param string $name
     * @param callable $value
     * @return Container
     */
    public function shareFactory(string $name, callable $value): Container
    {
        $this->share($name, $value);
        $this->factoryOnly[] = $name;

        return $this;
    }

    /**
     * Replaces the factory callback with the given name with the given factory callback.
     *
     * The new factory callback will receive as first argument the object created by the current
     * factory, and as second argument the container.
     *
     * @param string $name
     * @param callable $factory
     * @return Container
     * @throws Exception\WriteAccessOnLockedContainer
     * @throws Exception\NameNotFound
     * @throws Exception\NameOverwriteNotAllowed
     */
    public function extend(string $name, callable $factory): Container
    {
        if ($this->isLocked) {
            throw Exception\WriteAccessOnLockedContainer::forName($name);
        }

        if (!array_key_exists($name, $this->factories)) {
            throw Exception\NameNotFound::forName($name);
        }

        if (array_key_exists($name, $this->values)) {
            throw Exception\ExtendingResolvedNotAllowed::forName($name);
        }

        $currentFactory = $this->factories[$name];

        // phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
        $this->factories[$name] = static function (Container $container) use ($factory, $currentFactory) {
            return $factory($currentFactory($container), $container);
        };
        // phpcs:enable

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @see Container::has()
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     *
     * @see Container::get()
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     *
     * @see Container::addService()
     * @see Container::addValue()
     */
    public function offsetSet($offset, $value)
    {
        is_callable($value)
            ? $this->addService($offset, $value)
            : $this->addValue($offset, $value);
    }

    /**
     * Removing values or factory callbacks is not allowed.
     *
     * @param string $offset
     * @throws Exception\UnsetNotAllowed
     */
    public function offsetUnset($offset)
    {
        throw Exception\UnsetNotAllowed::forName($offset);
    }
}
