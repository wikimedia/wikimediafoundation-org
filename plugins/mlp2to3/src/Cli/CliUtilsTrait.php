<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Cli;

use cli\progress\Bar;
use Throwable;
use WP_CLI;
use function WP_CLI\Utils\make_progress_bar;

/**
 * WP-CLI related functionality.
 *
 * @package MultilingualPress2to3
 */
trait CliUtilsTrait
{
    /**
     * Outputs a success message.
     *
     * @param string $message The message to output.
     *
     * @return void
     *
     * @throws Throwable If problem outputting.
     */
    protected function _outputSuccess(string $message)
    {
        WP_CLI::success($message);
    }

    /**
     * Outputs a debug message.
     *
     * @param string $message The message to output.
     *
     * @return void
     *
     * @throws Throwable If problem outputting.
     */
    protected function _outputDebug(string $message)
    {
        WP_CLI::debug($message, false);
    }

    /**
     * Outputs a warning message.
     *
     * @param string $message The message to output.
     *
     * @return void
     *
     * @throws Throwable If problem outputting.
     */
    protected function _outputWarning(string $message)
    {
        WP_CLI::warning($message);
    }

    /**
     * Outputs an error message.
     *
     * @param string $message The message to output.
     *
     * @return void
     *
     * @throws Throwable If problem outputting.
     */
    protected function _outputError(string $message)
    {
        $messageLines = explode(PHP_EOL, $message);

        WP_CLI::error_multi_line($messageLines);
    }

    /**
     * Exits execution.
     *
     * @param int $code The exit code.
     *
     * @return void
     *
     * @throws Throwable If problem exiting.
     */
    protected function _exit(int $code)
    {
        WP_CLI::halt($code);
    }

    /**
     * Creates a new progress bar instance.
     *
     * @param string $message The progress message.
     * @param int $count The total number of progress ticks.
     *
     * @return Bar The new progress bar instance.
     *
     * @throws Throwable If problem creating.
     */
    protected function _createProgress(string $message, int $count)
    {
        return make_progress_bar($message, $count);
    }
}
