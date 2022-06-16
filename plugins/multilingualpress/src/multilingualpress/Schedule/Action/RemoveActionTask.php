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

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
use Inpsyde\MultilingualPress\Schedule\Schedule;
use Inpsyde\MultilingualPress\Schedule\Scheduler;
use Throwable;

/**
 * Class RemoveActionTask
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
class RemoveActionTask implements ActionTask
{
    use ThrowableHandleCapableTrait;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $scheduleIdName;

    /**
     * @var string
     */
    private $scheduleHook;

    /**
     * ScheduleRemoveAction constructor.
     * @param Request $request
     * @param Scheduler $scheduler
     * @param string $scheduleIdName
     * @param string $scheduleHook
     */
    public function __construct(
        Request $request,
        Scheduler $scheduler,
        string $scheduleIdName,
        string $scheduleHook
    ) {

        $this->request = $request;
        $this->scheduler = $scheduler;
        $this->scheduleIdName = $scheduleIdName;
        $this->scheduleHook = $scheduleHook;
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function execute()
    {
        $this->executeTasks();
    }

    /**
     * Execute Tasks
     * @throws ActionException
     */
    protected function executeTasks()
    {
        $this->deleteSchedule();
        $this->cleanScheduledEvents();
    }

    /**
     * Delete Schedule
     *
     * Remove the schedule Id from the list of the schedule id in the database
     *
     * @throws ActionException
     */
    protected function deleteSchedule()
    {
        $schedule = $this->schedule();

        if (!$this->scheduler->cleanup($schedule)) {
            throw ActionException::becauseScheduleCannotBeDeleted($schedule);
        }
    }

    /**
     * Retrieve the Schedule Id from the request
     *
     * @return Schedule
     * @throws ActionException
     */
    protected function schedule(): Schedule
    {
        $scheduleId = $this->scheduleIdFromRequest();
        $schedule = $this->scheduler->scheduleById($scheduleId);

        if (!$schedule instanceof Schedule) {
            throw ActionException::forInvalidScheduleId($scheduleId);
        }

        return $schedule;
    }

    /**
     * Retrieve the schedule Id from the current Request
     *
     * @return string
     */
    protected function scheduleIdFromRequest(): string
    {
        return (string)$this->request->bodyValue(
            $this->scheduleIdName,
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );
    }

    /**
     * Clean Scheduled Events
     *
     * Un-schedule all of the events for the copy attachment
     *
     * @return void
     * @throws ActionException
     */
    protected function cleanScheduledEvents()
    {
        $isEventUnscheduled = wp_unschedule_hook($this->scheduleHook);

        if (!$isEventUnscheduled) {
            throw ActionException::becauseUnscheduleHookFailForSchedule(
                $this->scheduleHook,
                $this->schedule()
            );
        }
    }
}
