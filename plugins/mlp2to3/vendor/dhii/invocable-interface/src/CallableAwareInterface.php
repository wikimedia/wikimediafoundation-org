<?php

namespace Dhii\Invocation;

/**
 * Something that can have a callable retrieved.
 *
 * @since [*next-version*]
 */
interface CallableAwareInterface
{
    /**
     * Retrieves the callable associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return callable|null The callable, if any.
     */
    public function getCallable();
}
