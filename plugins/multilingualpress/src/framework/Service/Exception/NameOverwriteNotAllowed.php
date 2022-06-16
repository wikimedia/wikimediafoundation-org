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

namespace Inpsyde\MultilingualPress\Framework\Service\Exception;

/**
 * Exception to be thrown when a value that has already been set is to be manipulated.
 */
class NameOverwriteNotAllowed extends InvalidValueAccess
{

    /**
     * @param string $name
     * @return NameOverwriteNotAllowed
     */
    public static function forServiceName(string $name): self
    {
        return new static(
            "Cannot set a service with name '{$name}'. A service with this name already exists."
        );
    }

    /**
     * @param string $name
     * @return NameOverwriteNotAllowed
     */
    public static function forValueName(string $name): self
    {
        return new static(
            "Cannot set a value with name '{$name}'. A value with this name already exists."
        );
    }
}
