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
 * Factory for WordPress error objects.
 */
class ErrorFactory
{
    /**
     * @var ClassResolver
     */
    private $classResolver;

    /**
     * @param ClassResolver $classResolver
     */
    public function __construct(ClassResolver $classResolver)
    {
        $this->classResolver = $classResolver;
    }

    /**
     * Returns a new WordPress error object, instantiated with the given arguments.
     *
     * @param array $args
     * @param string $class
     * @return \WP_Error
     */
    public function create(array $args = [], string $class = ''): \WP_Error
    {
        $class = $this->classResolver->resolve($class);

        return new $class(...$args);
    }
}
