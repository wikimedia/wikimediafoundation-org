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

use Inpsyde\MultilingualPress\Framework\Http\RequestHandler;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Framework\Nonce\Context;
use Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;

/**
 * Class ScheduleActionRequestHandler
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
class ScheduleActionRequestHandler implements RequestHandler
{
    use ThrowableHandleCapableTrait;
    use AuthorizationTrait;
    use ActionsProcessorTrait;

    const ACTION_AFTER_ACTION_DISPATCHED = 'multilingualpress.after_schedule_action_dispatched';
    const ACTION_NO_SCHEDULE_ACTION_DISPATCHED = 'multilingualpress.no_schedule_action_dispatched';

    /**
     * ActionsRequestHandler constructor.
     * @param Nonce $nonce
     * @param array $tasks
     * @param MessageFactoryInterface $messageFactory
     * @param Context $context
     * @param array $successMessages
     * @param string $actionName
     * @param string $userCapability
     */
    public function __construct(
        Nonce $nonce,
        array $tasks,
        MessageFactoryInterface $messageFactory,
        Context $context,
        array $successMessages,
        string $actionName,
        string $userCapability
    ) {

        $this->nonce = $nonce;
        $this->tasks = $tasks;
        $this->messageFactory = $messageFactory;
        $this->context = $context;
        $this->successMessages = $successMessages;
        $this->actionNameKey = $actionName;
        $this->userCapability = $userCapability;
    }

    /**
     * @inheritDoc
     * @param ServerRequest $request
     */
    public function handle(ServerRequest $request)
    {
        if (!$this->isUserAuthorized()) {
            return;
        }

        try {
            $this->process($request);
        } catch (\Throwable $throwable) {
            $this->logThrowable($throwable);
        }
    }
}
