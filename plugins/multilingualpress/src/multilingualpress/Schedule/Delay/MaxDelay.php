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

namespace Inpsyde\MultilingualPress\Schedule\Delay;

final class MaxDelay implements Delay
{
    /**
     * @var int
     */
    private $maxDelay;

    /**
     * @param int $maxDelay
     */
    public function __construct(int $maxDelay)
    {
        $this->maxDelay = abs($maxDelay);
    }

    /**
     * @param int $index
     * @param int $total
     * @param array $args
     * @return int
     */
    public function calculate(int $index, int $total, array $args = null): int
    {
        return (int)ceil(($this->maxDelay * $index) / $total);
    }
}
