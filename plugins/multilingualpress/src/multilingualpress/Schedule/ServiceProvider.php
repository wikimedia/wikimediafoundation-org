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
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;

/**
 * Service provider for all schedule objects.
 */
final class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container->share(
            Scheduler::class,
            static function (): Scheduler {
                return new Scheduler();
            }
        );

        $container->share(
            AjaxScheduleHandler::class,
            static function (Container $container): AjaxScheduleHandler {
                return new AjaxScheduleHandler(
                    $container[Scheduler::class],
                    $container[NonceFactory::class]
                );
            }
        );
    }

    /**
     * Bootstraps the registered services.
     *
     * @param Container $container
     */
    public function bootstrap(Container $container)
    {
        $ajaxScheduleHandler = $container[AjaxScheduleHandler::class];
        $serverRequest = $container[ServerRequest::class];

        add_action(
            Scheduler::ACTION_CLEANUP,
            static function ($scheduleId) use ($container) {
                \is_string($scheduleId) and $container[Scheduler::class]->cleanupIfDone($scheduleId);
            }
        );

        if (!wp_doing_ajax()) {
            return;
        }

        $ajaxActionHooks = [
            'wp_ajax_' . AjaxScheduleHandler::ACTION_SCHEDULE,
            'wp_ajax_nopriv_' . AjaxScheduleHandler::ACTION_SCHEDULE,
            'wp_ajax_' . AjaxScheduleHandler::ACTION_INFO,
            'wp_ajax_nopriv_' . AjaxScheduleHandler::ACTION_INFO,
        ];

        foreach ($ajaxActionHooks as $key) {
            add_action(
                $key,
                static function () use ($ajaxScheduleHandler, $serverRequest) {
                    $ajaxScheduleHandler->handle($serverRequest);
                }
            );
        }
    }
}
