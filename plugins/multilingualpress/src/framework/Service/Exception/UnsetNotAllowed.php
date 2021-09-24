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
class UnsetNotAllowed extends InvalidValueAccess
{

    /**
     * @param string $name
     * @return UnsetNotAllowed
     */
    public static function forName(string $name): self
    {
        return new static("Cannot unset {$name}. Removing items from container is not allowed.");
    }
}
