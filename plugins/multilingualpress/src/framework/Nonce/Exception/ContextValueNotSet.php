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
 * Exception to be thrown when a nonce context value that has not yet been set is to be read from
 * the container.
 */
class ContextValueNotSet extends \Exception
{

    /**
     * Returns a new exception object.
     *
     * @param string $name
     * @param string $action
     * @return ContextValueNotSet
     */
    public static function forName(
        string $name,
        string $action = 'read'
    ): ContextValueNotSet {

        return new static(
            sprintf(
                'Cannot %2$s "%1$s". There is no nonce context value with this name.',
                $name,
                $action
            )
        );
    }
}
