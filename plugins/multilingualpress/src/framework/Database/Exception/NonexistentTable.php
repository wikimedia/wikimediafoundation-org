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
 * Exception to be thrown when an action is to be performed on an table that doesn't exists.
 */
class NonexistentTable extends \Exception
{
    /**
     * NonexistentTable constructor.
     * @param string $action
     * @param string $table
     */
    public function __construct(string $action, string $table)
    {
        parent::__construct(
            sprintf('Cannot perform %1$s action. Table %2$s does not exists.', $action, $table)
        );
    }
}
