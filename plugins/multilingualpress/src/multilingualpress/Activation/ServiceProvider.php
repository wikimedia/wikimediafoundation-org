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

namespace Inpsyde\MultilingualPress\Activation;

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;

use const Inpsyde\MultilingualPress\ACTION_ACTIVATION;

/**
 * Service provider for all activation objects.
 */
final class ServiceProvider implements IntegrationServiceProvider
{
    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $container->addService(
            Activator::class,
            static function (): Activator {
                return new Activator();
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        $this->setupActivator($container);
    }

    /**
     * @param Container $container
     * @return void
     */
    private function setupActivator(Container $container)
    {
        $activator = $container[Activator::class];

        if (did_action(ACTION_ACTIVATION)) {
            $activator->handleActivation();
        }

        $activator->registerCallback(
            static function () use ($container) {

                $contentRelations = $container[ContentRelations::class];

                try {
                    $contentRelations->deleteAllRelationsForInvalidSites();
                    $contentRelations->deleteAllRelationsForInvalidContent(ContentRelations::CONTENT_TYPE_POST);
                    $contentRelations->deleteAllRelationsForInvalidContent(ContentRelations::CONTENT_TYPE_TERM);
                } catch (NonexistentTable $exc) {
                    return;
                }
            }
        );

        $activator->handlePendingActivation();
    }
}
