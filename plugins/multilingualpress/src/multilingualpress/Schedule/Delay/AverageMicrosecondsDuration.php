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
 * Class AverageMicrosecondsDuration
 * @package Inpsyde\MultilingualPress\Schedule\Delay
 */
final class AverageMicrosecondsDuration implements Delay
{
    const AVERAGE_MICROSECONDS_DEFAULT = 500;

    /**
     * @var int
     */
    private $secondsPerStep;

    /**
     * Creates an instance with default average of 500 microseconds, which means delay added will be
     * 1 seconds every 2000 steps.
     *
     * @return AverageMicrosecondsDuration
     */
    public static function default(): AverageMicrosecondsDuration
    {
        return new static(self::AVERAGE_MICROSECONDS_DEFAULT);
    }

    /**
     * @param int $microseconds
     */
    public function __construct(int $microseconds)
    {
        $this->secondsPerStep = abs($microseconds) / 1000000;
    }

    /**
     * @param int $index
     * @param int $total
     * @param array $args
     *
     * @return int
     */
    public function calculate(int $index, int $total, array $args = null): int
    {
        return (int)floor($this->secondsPerStep * $index);
    }
}
