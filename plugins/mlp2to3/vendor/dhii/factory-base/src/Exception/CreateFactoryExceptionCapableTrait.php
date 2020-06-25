<?php

namespace Dhii\Factory\Exception;

use Dhii\Factory\FactoryInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Functionality for creating factory exception instances.
 *
 * @since [*next-version*]
 */
trait CreateFactoryExceptionCapableTrait
{
    /**
     * Creates a new factory exception instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The exception message, if any.
     * @param string|Stringable|null $code     The exception code, if any.
     * @param RootException|null     $previous The previous exception for chaining, if any.
     * @param FactoryInterface|null  $factory  The factory instance, if any.
     *
     * @return FactoryExceptionInterface The created exception instance.
     */
    protected function _createFactoryException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $factory = null
    ) {
        return new FactoryException($message, $code, $previous, $factory);
    }
}
