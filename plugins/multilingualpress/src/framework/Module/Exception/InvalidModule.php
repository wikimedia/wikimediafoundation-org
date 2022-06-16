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

namespace Inpsyde\MultilingualPress\Framework\Module\Exception;

/**
 * Exception to be thrown when a module that does not exist is to be manipulated.
 */
class InvalidModule extends \Exception
{

    /**
     * Returns a new exception object.
     *
     * @param string $moduleId
     * @param string $action
     * @return InvalidModule
     */
    public static function forId(string $moduleId, string $action = 'read'): self
    {
        return new static(
            sprintf(
                'Cannot %2$s "%1$s". There is no module with this ID.',
                $moduleId,
                $action
            )
        );
    }
}
