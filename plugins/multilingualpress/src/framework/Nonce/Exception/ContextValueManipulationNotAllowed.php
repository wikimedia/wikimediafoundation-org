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

namespace Inpsyde\MultilingualPress\Framework\Nonce\Exception;

/**
 * Exception to be thrown when a nonce context value is to be manipulated.
 */
class ContextValueManipulationNotAllowed extends \Exception
{

    /**
     * Returns a new exception object.
     *
     * @param string $name
     * @param string $action
     * @return ContextValueManipulationNotAllowed
     */
    public static function forName(
        string $name,
        string $action = 'set'
    ): ContextValueManipulationNotAllowed {

        return new static(
            sprintf(
                'Cannot %2$s "%1$s". Manipulating a nonce context value is not allowed.',
                $name,
                $action
            )
        );
    }
}
