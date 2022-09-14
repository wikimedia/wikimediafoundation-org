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

use WP_CLI;

/**
 * @psalm-type Arg = array{type: string, name: string, description?: string, optional?: bool, options?: list<string>}
 * @psalm-type Doc = array{shortdesc?: string, synopsis?: list<Arg>, when?: string, longdesc?: string}
 */
interface WpCliCommand
{
    /**
     * The handler of
     * {@link https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-command/ WP_CLI::add_command}
     * implementation.
     *
     * @param array<string> $args The list of positional arguments
     * @param array<string, scalar> $associativeArgs A map of associative argument names to values
     * @return void
     * @throws WP_CLI\ExitException
     */
    public function handler(array $args, array $associativeArgs): void;

    /**
     * The command documentation.
     *
     * @psalm-return Doc A map of
     * {@link https://make.wordpress.org/cli/handbook/references/documentation-standards/ command doc} names to values
     * @return array<string, string|array> A map of
     * {@link https://make.wordpress.org/cli/handbook/references/documentation-standards/ command doc} names to values
     */
    public function docs(): array;

    /**
     * The Name of the command
     *
     * @return string The Name of the command
     */
    public function name(): string;
}
