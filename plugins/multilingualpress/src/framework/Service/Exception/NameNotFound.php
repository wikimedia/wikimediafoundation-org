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
 * Exception to be thrown when a value or factory callback could not be found in the container.
 */
class NameNotFound extends InvalidValueReadAccess
{

    /**
     * Returns a new exception object.
     *
     * @param string $name
     * @return self
     */
    public static function forName(string $name): self
    {
        return new static("There is neither a value or service named '{$name}'.");
    }
}
