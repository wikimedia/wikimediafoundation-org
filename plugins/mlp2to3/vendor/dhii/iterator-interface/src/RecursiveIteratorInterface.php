<?php

namespace Dhii\Iterator;

use RecursiveIteratorIterator as I;

/**
 * An iterator which visits every node of a nested structure.
 *
 * Although the loop flattens the structure hierarchy, it is still possible
 * to reconstruct the hierarchy by using the iteration's path segments.
 *
 * @since [*next-version*]
 */
interface RecursiveIteratorInterface extends IteratorInterface
{
    /**
     * Instructs the iterator to only visit leaf nodes.
     *
     * @since [*next-version*]
     */
    const MODE_LEAVES_ONLY = I::LEAVES_ONLY;

    /**
     * Instructs the iterator to iterate over parents before children.
     *
     * @since [*next-version*]
     */
    const MODE_SELF_FIRST = I::SELF_FIRST;

    /**
     * Instructs the iterator to iterate over children before parents.
     *
     * @since [*next-version*]
     */
    const MODE_CHILD_FIRST = I::CHILD_FIRST;

    /**
     * {@inheritdoc}
     * 
     * @since [*next-version*]
     *
     * @return RecursiveIterationInterface The current iteration.
     */
    public function getIteration();
}
