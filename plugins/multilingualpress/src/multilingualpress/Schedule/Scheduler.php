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

namespace Inpsyde\MultilingualPress\Schedule;

/**
 * Make easier the schedule (via cron) of asynchronous tasks.
 *
 * For example:
 *
 * ```
 * $scheduler = new Scheduler();
 *
 * $ids = someFunctionThatCalculatesPostIdsToProcess();
 *
 * if ($ids && !get_option('my-schedule-is-running')) {
 *
 *     $scheduleId = $scheduler->newSchedule(count($ids), 'my-schedule-hook', $ids);
 *
 *     update_option('my-schedule-is-running', $scheduleId);
 * }
 *
 * add_action('my-schedule-hook', function ($scheduleArgs) use ($scheduler) {
 *
 *     list(
 *         $schedule,  // A Schedule object
 *         $step,      // The (zero-based) index for the step running
 *         $args       // The array of args for the schedule
 *     ) = $scheduler->parseScheduleHookParam($scheduleArgs);
 *
 *     if (!$schedule
 *         || get_option('my-schedule-is-running') !== $schedule->id()
 *         || $schedule->isDone()
 *         || empty($args[$step])
 *     ) {
 *         return;
 *     }
 *
 *     // Something like "X minutes", "or X hours", or "1 minute", properly translated.
 *     // For the first step it will be "Unknown" (translated).
 *     echo 'Estimated remaining time: ' . $schedule->estimatedRemainingTime();
 *
 *     $postToProcess = get_post($args[$step]);
 *
 *     // ... Process post here...
 *
 *     $scheduler->stepDone($schedule);  // This is **REQUIRED**
 *
 *     // In case what we just completed was the last step...
 *     if ($schedule->isDone()) {
 *         delete_option('my-schedule-is-running');
 *
 *         // We're doing this manually, but even if we don't, it'll be done via cron in 24 hours
 *         $scheduler->cleanup($schedule);
 *     }
 * }
 * ```
 *
 * So the hook is fired once per each step, passing the current step index as hook argument.
 *
 * The schedule object (among others) has a method to inform about its status, and a method to
 * inform about the estimated remaining time.
 *
 * Every time `$scheduler->stepDone` is called, the schedule is updated increasing the count of done
 * steps and a "last update" property is updated so that estimated remaining time can be calculated.
 * Plus the schedule can be marked as done when all steps are completed.
 *
 * Note that every time `$scheduler->newSchedule()` is called a *new* schedule is created, this is
 * why in the example the schedule id is stored in an option: to avoid to schedule more processes.
 *
 * @package Inpsyde\MultilingualPress\Schedule
 */
class Scheduler
{
    const OPTION = 'multilingualpress_cron_schedules';

    const ACTION_CLEANUP = 'multilingualpress.done-schedule-cleanup';
    const ACTION_SCHEDULED = 'multilingualpress.cron-scheduled';

    /**
     * Creates a new multi-step schedule.
     *
     * @param int $stepsCount
     * @param string $hook
     * @param array $args
     * @param Delay\Delay|null $delay
     * @return string
     */
    public function newSchedule(
        int $stepsCount,
        string $hook,
        array $args = [],
        Delay\Delay $delay = null
    ): string {

        $schedule = Schedule::newMultiStepInstance($stepsCount, $delay, $args);

        return $this->persist($schedule, $stepsCount, $hook, $args) ? $schedule->id() : '';
    }

    /**
     * Tells scheduler that a step for given schedule just completed.
     *
     * @param Schedule $schedule
     * @return Schedule
     */
    public function stepDone(Schedule $schedule): Schedule
    {
        if ($schedule->isDone()) {
            return $schedule;
        }

        $this->persist($schedule->nextStep());

        return $schedule;
    }

    /**
     * Cleanups schedule of given ID.
     *
     * Use responsibly, will break running schedules.
     * `Scheduler::cleanupIfDone` can be used to only cleanup a schedule if completed.
     *
     * @param Schedule $schedule
     * @return bool
     */
    public function cleanup(Schedule $schedule): bool
    {
        $schedules = get_option(self::OPTION, []) ?: [];
        unset($schedules[$schedule->id()]);

        if (!$schedules) {
            return delete_option(self::OPTION);
        }

        return update_option(self::OPTION, $schedules, false);
    }

    /**
     * Cleanups schedule of given ID only if done.
     *
     * This is run via cron 24h after the schedule is marked as complete.
     *
     * @param string $scheduleId
     * @return bool
     */
    public function cleanupIfDone(string $scheduleId): bool
    {
        $schedule = $this->scheduleById($scheduleId);

        return $schedule && $schedule->isDone() && $this->cleanup($schedule);
    }

    /**
     * @param \stdClass $param
     * @return array
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function parseScheduleHookParam($param): array
    {
        // phpcs:disable

        if (!$param instanceof \stdClass) {
            return [null, null, null];
        }

        $scheduleId = $param->schedule ?? null;
        $step = $param->step ?? null;
        $args = $param->args ?? null;

        if (!$scheduleId || !\is_string($scheduleId) || !is_numeric($step) || !\is_array($args)) {
            return [null, null, null];
        }

        $schedule = $this->scheduleById($scheduleId);
        if (!$schedule) {
            return [null, null, null];
        }

        return [$schedule, (int)$step, $args];
    }

    /**
     * Loads a Schedule object form its ID.
     *
     * @param string $scheduleId
     * @return Schedule|null
     */
    public function scheduleById(string $scheduleId)
    {
        $all = get_option(self::OPTION, []) ?: [];
        $data = $all[$scheduleId] ?? null;
        if (!$data || !\is_array($data)) {
            return null;
        }

        try {
            $schedule = Schedule::fromArray($data);
        } catch (\Throwable $exception) {
            return null;
        }

        return $schedule;
    }

    /**
     * @param Schedule $schedule
     * @param int $steps
     * @param string $hook
     * @param array $args
     * @return bool
     */
    private function persist(
        Schedule $schedule,
        int $steps = null,
        string $hook = null,
        array $args = null
    ): bool {

        $id = $schedule->id();

        $schedules = (array)(get_option(self::OPTION, []) ?: []);
        $schedules[$id] = $schedule->toArray();

        // Turn installing off because of problems in VipGo
        $wpInstalling = wp_installing(false);
        $updated = update_option(self::OPTION, $schedules, false);
        wp_installing($wpInstalling);

        if ($steps && $hook) {
            do_action(self::ACTION_SCHEDULED, $hook, $id, $steps);
            do_action(self::ACTION_SCHEDULED . "_{$hook}", $id, $steps);

            $params = (object)['args' => $args, 'schedule' => $id];
            $delay = $schedule->delay();
            for ($i = 0; $i < $steps; $i++) {
                $params->step = $i;
                $when = time() + $delay->calculate($i, $steps, $args);
                wp_schedule_single_event($when, $hook, [$params]);
            }

            return $updated;
        }

        if ($schedule->isDone()) {
            // Cleanup completed schedules after 24 hours
            wp_schedule_single_event(time() + DAY_IN_SECONDS, self::ACTION_CLEANUP, [$id]);
        }

        return $updated;
    }
}
