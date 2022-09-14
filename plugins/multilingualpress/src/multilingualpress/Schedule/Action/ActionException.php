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

namespace Inpsyde\MultilingualPress\Schedule\Action;

use Exception;
use Inpsyde\MultilingualPress\Schedule\Schedule;

/**
 * Class ActionException
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
class ActionException extends Exception
{
    /**
     * @param Schedule $schedule
     * @return ActionException
     */
    public static function becauseScheduleCannotBeDeleted(
        Schedule $schedule
    ): ActionException {

        $message = sprintf(/* translators: %s is the name of the action which cannot be deleted */
            esc_html_x(
                'Schedule with Id: %s cannot be deleted.',
                'Site Duplication',
                'multilingualpress'
            ),
            $schedule->id()
        );

        return new self($message);
    }

    /**
     * @param string $scheduleId
     * @return ActionException
     */
    public static function forInvalidScheduleId(string $scheduleId): ActionException
    {
        $message = sprintf(/* translators: %s is the name of the invalid action */
            esc_html_x(
                'Retrieved invalid schedule by id: %s',
                'Site Duplication',
                'multilingualpress'
            ),
            $scheduleId
        );

        return new self($message);
    }

    /**
     * @param string $hook
     * @param Schedule $schedule
     * @return ActionException
     */
    public static function becauseUnscheduleHookFailForSchedule(
        string $hook,
        Schedule $schedule
    ): ActionException {

        return new self("Unschedule {$hook} event failed for schedule with Id {$schedule->id()}");
    }
}
