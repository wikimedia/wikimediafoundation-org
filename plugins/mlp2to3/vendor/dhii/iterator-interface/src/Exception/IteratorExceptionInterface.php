<?php

namespace Dhii\Iterator\Exception;

use Dhii\Iterator\IteratorAwareInterface;

/**
 * An exception that occurs in relation to an iterator.
 *
 * @since [*next-version*]
 */
interface IteratorExceptionInterface extends
        IteratingExceptionInterface,
        IteratorAwareInterface
{
}
