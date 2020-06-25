<?php

namespace Dhii\Iterator;

/**
 * Something that can have an iterator retrieved from it.
 *
 * @since [*next-version*]
 */
interface IteratorAwareInterface
{
    /**
     * Retrieves the iterator associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return IteratorInterface|null The iterator, if any.
     */
    public function getIterator();
}
