<?php

namespace Dhii\Invocation;

/**
 * Something that can have arguments retrieved.
 *
 * @since [*next-version*]
 */
interface ArgsAwareInterface
{
    /**
     * Retrieves the arguments associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return array The arguments.
     */
    public function getArgs();
}
