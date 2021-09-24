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

namespace Inpsyde\MultilingualPress\Framework\Auth;

use RuntimeException;

/**
 * Class AuthFactoryException
 * @package Inpsyde\MultilingualPress\Framework\Auth
 */
class AuthFactoryException extends RuntimeException
{
    /**
     * Create a new Exception because Entity is not valid
     *
     * @return AuthFactoryException
     */
    public static function becauseEntityIsInvalid(): self
    {
        return new self('Cannot create an Auth because given entity is not valid');
    }
}
