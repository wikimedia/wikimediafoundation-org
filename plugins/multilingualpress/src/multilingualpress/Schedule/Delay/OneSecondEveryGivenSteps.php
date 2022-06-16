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

/**
 * Class OneSecondEveryGivenSteps
 * @package Inpsyde\MultilingualPress\Schedule\Delay
 */
final class OneSecondEveryGivenSteps implements Delay
{
    const EVERY_STEPS_DEFAULT = 300;

    /**
     * @var int
     */
    private $everySteps;

    /**
     * @return OneSecondEveryGivenSteps
     */
    public static function default(): OneSecondEveryGivenSteps
    {
        return new static(self::EVERY_STEPS_DEFAULT);
    }

    /**
     * @param int $everySteps
     */
    public function __construct(int $everySteps)
    {
        $this->everySteps = $everySteps;
    }

    /**
     * @param int $index
     * @param int $total
     * @param array $args
     * @return int
     */
    public function calculate(int $index, int $total, array $args = null): int
    {
        return (int)(floor($index / $this->everySteps) - 1);
    }
}
