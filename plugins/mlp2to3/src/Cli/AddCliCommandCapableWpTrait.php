<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Cli;

use Exception;
use Throwable;
use WP_CLI;

/**
 * Functionality for adding commands to WP CLI.
 *
 * @package MultilingualPress2to3
 */
trait AddCliCommandCapableWpTrait
{
    /**
     * Registers a CLI command handler.
     *
     * @param string $command The command.
     * Sub-commands should be separated by a space, i.e. `command sub-command`.
     * @param callable $handler The command handler.
     *
     * @throws Throwable If problems adding command.
     */
    protected function _addCliCommand(string $command, callable $handler, array $documentation = [])
    {
        if (!class_exists('WP_CLI')) {
            throw new Exception($this->__('Could not add command "%1$s": Not a CLI environment', [$command]));
        }

        WP_CLI::add_command($command, $handler, $documentation);
    }

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
