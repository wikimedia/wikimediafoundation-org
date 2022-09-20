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

namespace Inpsyde\MultilingualPress\WpCli;

use Exception;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\LateAccessToNotSharedService;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameNotFound;

class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container->share(
            WpCliCommandsHelper::class,
            static function (): WpCliCommandsHelper {
                return new WpCliCommandsHelper();
            }
        );

        /**
         * A list of available MLP CLI commands
         */
        $container->share(
            'multilingualpress.WpCli.WpCliCommands',
            static function (): array {
                return [];
            }
        );
    }

    /**
     * @inheritDoc
     * @throws NameNotFound
     * @throws LateAccessToNotSharedService
     * @throws Exception
     */
    public function bootstrap(Container $container)
    {
        if (!defined('WP_CLI') || !WP_CLI) {
            return;
        }

        $wpCliCommands = $container->get('multilingualpress.WpCli.WpCliCommands');
        $wpCliCommandsHelper = $container->get(WpCliCommandsHelper::class);

        $this->registerCliCommands($wpCliCommands, $wpCliCommandsHelper);
    }

    /**
     * Register Cli commands for MLP
     *
     * @param iterable<WpCliCommand> $wpCliCommands
     * @param WpCliCommandsHelper $wpCliCommandsHelper
     * @throws Exception
     */
    protected function registerCliCommands(iterable $wpCliCommands, WpCliCommandsHelper $wpCliCommandsHelper)
    {
        foreach ($wpCliCommands as $command) {
            $wpCliCommandsHelper->addCliCommand(
                "mlp {$command->name()}",
                static function (array $positionalArgs, array $associativeArgs) use ($command) {
                    $command->handler($positionalArgs, $associativeArgs);
                },
                $command->docs()
            );
        }
    }
}
