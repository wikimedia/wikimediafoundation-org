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

interface Delay
{
    /**
     * Calculates the delay in seconds that should be used to setup the cron event for each index,
     * providing the total steps and schedule arguments as context.
     *
     * @param int $index
     * @param int $total
     * @param array|null $args
     * @return int
     */
    public function calculate(int $index, int $total, array $args = null): int;
}
