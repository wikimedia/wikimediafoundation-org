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

use Inpsyde\MultilingualPress\Framework\Language\Language;

class LanguageFactory
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
     * Returns a new language object of the given (or default) class,
     * instantiated with the given arguments.
     *
     * @param array $args
     * @param string|null $class
     * @return Language
     */
    public function create(array $args = [], string $class = null): Language
    {
        $class = $this->classResolver->resolve($class);

        return new $class(...$args);
    }
}
