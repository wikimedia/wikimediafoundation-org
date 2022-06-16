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
 * Exception to be thrown when a locked container is to be manipulated.
 */
class WriteAccessOnLockedContainer extends NameOverwriteNotAllowed
{

    /**
     * @param string $name
     * @return WriteAccessOnLockedContainer
     */
    public static function forName(string $name): self
    {
        return new static(
            "Cannot access {$name} for writing. Manipulating a locked container is not allowed."
        );
    }
}
