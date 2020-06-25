<?php

namespace Dhii\Iterator;

use Dhii\Iterator\Exception\IteratorException;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Common functionality for objects that can create iterator exceptions.
 *
 * @since [*next-version*]
 */
trait CreateIteratorExceptionCapableTrait
{
    /**
     * Creates a new iterator exception instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The previous exception for chaining, if any.
     * @param IteratorInterface      $iterator The iterator instance that erred.
     *
     * @return IteratorException The created exception instance.
     */
    protected function _createIteratorException(
        $message = null,
        $code = null,
        RootException $previous = null,
        IteratorInterface $iterator
    ) {
        return new IteratorException($message, $code, $previous, $iterator);
    }
}
