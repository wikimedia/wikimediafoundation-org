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

namespace Inpsyde\MultilingualPress\Framework\Database\Exception;

/**
 * Exception to be thrown when an action is to be performed on an invalid table.
 */
class InvalidTable extends \Exception
{
    /**
     * Returns a new exception object.
     *
     * @param string $action
     * @return InvalidTable
     */
    public static function forAction(string $action = 'install'): InvalidTable
    {
        return new static(sprintf('Cannot %s. Table invalid.', $action));
    }
}
