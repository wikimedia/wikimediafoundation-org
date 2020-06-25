<?php

namespace Dhii\Iterator;

use Iterator;
use Dhii\Iterator\Exception\IteratorExceptionInterface;

/**
 * Something that can be iterated over.
 *
 * For every iteration, allows retrieval of state of that iteration, which
 * provides at least the key and the value of the current item.
 *
 * This purpose of this mechanism is to solve an inherent problem of
 * sophisticated PHP/SPL interfaces, which store state in themselves,
 * preventing dependency injection, and proper SoC by conflicting with
 * other helpful interfaces.
 *
 * @since [*next-version*]
 */
interface IteratorInterface extends
        Iterator,
        IterationAwareInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws IteratorExceptionInterface If something goes wrong while advancing.
     */
    public function next();

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws IteratorExceptionInterface If something goes wrong while rewinding.
     */
    public function rewind();
}
