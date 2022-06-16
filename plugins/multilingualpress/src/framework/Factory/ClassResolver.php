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

namespace Inpsyde\MultilingualPress\Framework\Factory;

/**
 * Class to be used for class resolution in factories.
 */
class ClassResolver
{
    /**
     * @var string
     */
    private $base;

    /**
     * @var bool
     */
    private $baseIsClass;

    /**
     * @var string
     */
    private $defaultClass;

    /**
     * @param string $base
     * @param string|null $defaultClass
     * @throws \InvalidArgumentException If the given base is not a valid fully qualified class
     */
    public function __construct(string $base, string $defaultClass = null)
    {
        $this->base = $base;
        $this->baseIsClass = class_exists($base);

        if (!$this->baseIsClass && !interface_exists($base)) {
            throw new \InvalidArgumentException(
                __METHOD__ . ' requires a valid fully qualified class or interface name as first argument.'
            );
        }

        if ($defaultClass) {
            $this->defaultClass = $this->checkClass($defaultClass);
        } elseif ($this->baseIsClass) {
            $this->defaultClass = $base;
        }
    }

    /**
     * Resolves the class to be used for instantiation, which might be either the given class or
     * the default class.
     *
     * @param string|null $class
     * @return string
     * @throws \InvalidArgumentException If no class is given and no default class is available.
     */
    public function resolve(string $class = null): string
    {
        if ($class && $class !== $this->defaultClass) {
            return $this->checkClass($class);
        }

        if (!$this->defaultClass) {
            throw new \InvalidArgumentException(
                'Cannot resolve class name if no class is given and no default class is available.'
            );
        }

        return $this->defaultClass;
    }

    /**
     * Checks if the class with the given name is valid with respect to the defined base.
     *
     * @param string $class
     * @return string
     * @throws Exception\InvalidClass If the given class is invalid with respect to the defined base.
     */
    private function checkClass(string $class): string
    {
        if ($this->baseIsClass && $class === $this->base) {
            return $class;
        }

        if (!class_exists($class) || !is_subclass_of($class, $this->base, true)) {
            throw new Exception\InvalidClass(
                "The class '{$class}' is invalid with respect to the defined base '{$this->base}'."
            );
        }

        return $class;
    }
}
