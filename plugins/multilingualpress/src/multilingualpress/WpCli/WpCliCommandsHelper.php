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

use Inpsyde\MultilingualPress\Framework\Integration\Integration;
use Exception;
use Throwable;
use WP_CLI;

/**
 * WP-CLI commands helper.
 */
class WpCliCommandsHelper
{
    /**
     * Registers a CLI command handler.
     *
     * @param string $command The command.
     * Sub-commands should be separated by a space, i.e. `command sub-command`.
     * @param callable $handler The command handler.
     * @throws Exception
     */
    public function addCliCommand(string $command, callable $handler, array $documentation = []): void
    {
        WP_CLI::add_command($command, $handler, $documentation);
    }

    /**
     * Display an error message
     *
     * @param string $message An error message.
     * @throws WP_CLI\ExitException
     */
    public function showCliError(string $message): void
    {
        WP_CLI::error($message);
    }

    /**
     * Display a success message
     *
     * @param string $message An error message.
     */
    public function showCliSuccess(string $message): void
    {
        WP_CLI::success($message);
    }
}
