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

use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Nonce\Context;

/**
 * Used to simplify AJAX interaction with scheduler.
 *
 * It provides two methods to get URLs of:
 * - an AJAX endpoint to generate a schedule for given hook, steps and args
 * - an AJAX endpoint to get information about a schedule of given ID.
 * Both URLS are nonced.
 *
 * The class also ships the handler for the two AJAX actions, registered by the service provider to
 * respond to them.
 */
class AjaxScheduleHandler
{
    const ACTION_SCHEDULE = 'multilingualpress_ajax_cron_schedule';
    const ACTION_INFO = 'multilingualpress_ajax_cron_schedule_info';

    const FILTER_AJAX_SCHEDULE_DELAY = 'multilingualpress.ajax_schedule_delay';

    const MODE_PUBLIC = 'public';
    const MODE_RESTRICTED = 'restricted';

    const SCHEDULE_ID = 'schedule-id';
    const SCHEDULE_STEPS = 'schedule-steps';
    const SCHEDULE_HOOK = 'schedule-hook';
    const SCHEDULE_ARGS = 'schedule-args';

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var NonceFactory
     */
    private $factory;

    /**
     * @param Scheduler $scheduler
     * @param NonceFactory $factory
     */
    public function __construct(Scheduler $scheduler, NonceFactory $factory)
    {
        $this->scheduler = $scheduler;
        $this->factory = $factory;
    }

    /**
     * Generate an AJAX URL whose handler will generate a new schedule.
     *
     * Steps, hook, and args, necessary to build the schedule can be passed here to be already
     * included in the URL, or can be ignored here and passed later with the request.
     *
     * @param int $steps
     * @param string $hook
     * @param array|null $args
     * @param string $mode
     * @return string
     */
    public function scheduleAjaxUrl(
        int $steps = null,
        string $hook = null,
        array $args = null,
        string $mode = self::MODE_RESTRICTED
    ): string {

        $data = [
            'action' => self::ACTION_SCHEDULE,
            self::ACTION_SCHEDULE => (string)$this->factory->create([self::ACTION_SCHEDULE]),
            self::MODE_PUBLIC => $mode === self::MODE_PUBLIC,
        ];

        ($steps === null) or $data[self::SCHEDULE_STEPS] = $steps;
        ($hook === null) or $data[self::SCHEDULE_HOOK] = $hook;
        ($args === null) or $data[self::SCHEDULE_ARGS] = $args;

        return add_query_arg($data, admin_url('admin-ajax.php'));
    }

    /**
     * Generate an AJAX URL whose handler will provide information for a schedule.
     *
     * Schedule ID, necessary to retrieve the schedule can be passed here to be already
     * included in the URL, or can be ignored here and passed later with the request.
     *
     * @param string $scheduleId
     * @param string $mode
     * @return string
     */
    public function scheduleInfoAjaxUrl(
        string $scheduleId = null,
        string $mode = self::MODE_RESTRICTED
    ): string {

        $data = [
            'action' => self::ACTION_INFO,
            self::ACTION_INFO => (string)$this->factory->create([self::ACTION_INFO]),
            self::MODE_PUBLIC => $mode === self::MODE_PUBLIC,
        ];

        ($scheduleId === null) or $data[self::SCHEDULE_ID] = $scheduleId;

        return add_query_arg($data, admin_url('admin-ajax.php'));
    }

    /**
     * Handler for both AJAX actions managed by this class.
     *
     * Checks action and nonce, then dispatch the request to the specific handling method.
     *
     * @param ServerRequest $request
     * @param Context|null $context
     * @return void
     */
    public function handle(ServerRequest $request, Context $context = null)
    {
        $action = $request->bodyValue('action', INPUT_REQUEST, FILTER_SANITIZE_STRING);
        if (!$action || !\in_array($action, [self::ACTION_INFO, self::ACTION_SCHEDULE], true)) {
            wp_send_json_error(esc_html__('Invalid action.', 'multilingualpress'));
        }

        $public = $request->bodyValue(self::MODE_PUBLIC, INPUT_REQUEST, FILTER_SANITIZE_STRING);
        if (!$public && !is_user_logged_in()) {
            wp_send_json_error(esc_html__('User not allowed.', 'multilingualpress'));
        }

        if (!$action || !$this->factory->create([$action])->isValid($context)) {
            wp_send_json_error(esc_html__('User not allowed.', 'multilingualpress'));
        }

        if ($action === self::ACTION_INFO) {
            $this->sendScheduleInfo($request);
        }

        $this->createNewSchedule($request);
    }

    /**
     * Handling method for information about a schedule.
     *
     * @param ServerRequest $request
     */
    private function sendScheduleInfo(ServerRequest $request)
    {
        $id = $request->bodyValue(self::SCHEDULE_ID, INPUT_REQUEST, FILTER_SANITIZE_STRING);
        if (!$id) {
            wp_send_json_error(
                [
                    'message' => esc_html__('Missing schedule ID.', 'multilingualpress'),
                ]
            );
        }

        $schedule = $this->scheduler->scheduleById($id);
        if (!$schedule) {
            wp_send_json_error(
                [
                    'message' => esc_html__('Schedule not found', 'multilingualpress'),
                ]
            );
        }

        wp_send_json_success(
            [
                'scheduleId' => $schedule->id(),
                'schedule' => $schedule->toArray(),
                'stepToFinish' => $schedule->stepToFinish(),
                'estimatedRemainingTime' => esc_html($schedule->estimatedRemainingTime()),
            ]
        );
    }

    /**
     * Handling method for schedule generation.
     *
     * @param ServerRequest $request
     */
    private function createNewSchedule(ServerRequest $request)
    {
        $hook = $request->bodyValue(self::SCHEDULE_HOOK, INPUT_REQUEST, FILTER_SANITIZE_STRING);
        $steps = $request->bodyValue(self::SCHEDULE_STEPS, INPUT_REQUEST, FILTER_VALIDATE_INT);
        if (!$hook || !$steps) {
            wp_send_json_error(esc_html__('Missing schedule data.', 'multilingualpress'));
        }

        $args = $request->bodyValue(self::SCHEDULE_ARGS, INPUT_REQUEST) ?: [];

        $hook = (string)$hook;
        $steps = (int)$steps;
        $args = (array)$args;

        /**
         * Filter the schedule delay.
         *
         * Schedule delay is used to define when the next single cron event have to be executed.
         * @see \Inpsyde\MultilingualPress\Schedule\Scheduler::persist
         *
         * @param null The NULL value to override to use custom delay
         * @param string $hook The hook to use
         * @param int $steps The steps for the schedule
         * @param array $args The arguments to pass to the callback
         */
        $delay = apply_filters(self::FILTER_AJAX_SCHEDULE_DELAY, null, $hook, $steps, $args);
        ($delay && $delay instanceof Delay\Delay) or $delay = null;

        /*
         * This is should be probably saved somewhere, or it will be lost, because next statement
         * will die the request.
         * Use `Scheduler::ACTION_SCHEDULED` hook for the purpose.
         */
        $scheduleId = $this->scheduler->newSchedule($steps, $hook, $args, $delay);

        wp_send_json_success($scheduleId);
    }
}
