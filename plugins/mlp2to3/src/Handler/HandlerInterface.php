<?php

namespace Inpsyde\MultilingualPress2to3\Handler;

use Exception;

/**
 * Represents a handler.
 *
 * Typically, this is the "controller" layer, and is used to integrate
 * de-coupled functionality into a framework.
 *
 * @package MultilingualPress2to3
 */
interface HandlerInterface
{
    /**
     * Runs the handler.
     *
     * @throws Exception If problem running.
     *
     * @return mixed The result of running the handler.
     */
    public function run();
}
