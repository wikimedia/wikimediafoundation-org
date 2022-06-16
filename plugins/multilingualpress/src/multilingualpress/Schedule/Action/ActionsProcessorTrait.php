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

use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface;

/**
 * Trait ActionsProcessorTrait
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
trait ActionsProcessorTrait
{
    use ResponseTrait;

    /**
     * @var array
     */
    private $tasks;

    /**
     * @var MessageFactoryInterface
     */
    private $messageFactory;

    /**
     * @var string
     */
    private $actionNameKey;

    /**
     * @param ServerRequest $request
     * @throws \Exception
     */
    protected function process(ServerRequest $request)
    {
        $errors = [];
        $actionName = $this->actionNameFrom($request);
        $tasks = $this->tasks[$actionName] ?? [];

        if (!$tasks) {
            /**
             * After task has been dispatched
             *
             * @params string $task the dispatched task name
             */
            do_action(self::ACTION_NO_SCHEDULE_ACTION_DISPATCHED, $actionName);
            return;
        }

        /** @var ActionTask $task */
        foreach ($tasks as $task) {
            try {
                $task instanceof ActionTask and $task->execute();
            } catch (\Throwable $throwable) {
                $errors[] = $this->messageFactory->create(
                    'error',
                    $throwable->getMessage(),
                    []
                );
            }
        }

        /**
         * After task has been dispatched
         *
         * @params string $task The dispatched task name
         */
        do_action(self::ACTION_AFTER_ACTION_DISPATCHED, $actionName);

        /**
         * After task has been dispatched
         */
        do_action(self::ACTION_AFTER_ACTION_DISPATCHED . "_{$actionName}");

        $this->sendResponseFor($actionName, $errors);
    }

    /**
     * @param ServerRequest $request
     * @return string
     */
    protected function actionNameFrom(ServerRequest $request): string
    {
        return (string)$request->bodyValue($this->actionNameKey, INPUT_POST, FILTER_SANITIZE_STRING);
    }
}
