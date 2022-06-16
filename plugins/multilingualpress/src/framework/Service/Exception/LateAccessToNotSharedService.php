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
 * Exception to be thrown when a not shared value or factory callback is to be accessed on a
 * bootstrapped container.
 */
class LateAccessToNotSharedService extends InvalidValueReadAccess
{

    /**
     * @param string $name
     * @param string $action
     * @return self
     */
    public static function forService(string $name, string $action): self
    {
        return new static(
            sprintf(
                'Cannot %2$s not shared "%1$s". The container has already been bootstrapped.',
                $name,
                $action
            )
        );
    }
}
