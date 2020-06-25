<?php

namespace Dhii\Invocation\Exception;

use Dhii\Exception\AbstractBaseException;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Invocation\ArgsAwareTrait;
use Dhii\Invocation\CallbackAwareTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Concrete implementation for an exception that occurs in relation to an invocation.
 *
 * @since [*next-version*]
 */
class InvocationException extends AbstractBaseException implements InvocationExceptionInterface
{
    /*
     * Provides awareness of, and storage functionality for, a callable.
     *
     * @since [*next-version*]
     */
    use CallbackAwareTrait {
        _getCallback as public getCallable;
    }

    /*
     * Provides awareness of, and storage functionality, for arguments.
     *
     * @since [*next-version*]
     */
    use ArgsAwareTrait {
        _getArgs as public getArgs;
    }

    /*
     * Adds string normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /*
     * Adds integer normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /*
     * Provides iterable normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /*
     * Provides string translating capabilities.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /*
     * Provides functionality for creating invalid argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The previous exception for chaining, if any.
     * @param callable|null          $callable The callable that was invoked and erred, if any.
     * @param array|null             $args     The arguments that the callable was invoked with, if any.
     */
    public function __construct(
        $message = null,
        $code = null,
        RootException $previous = null,
        callable $callable = null,
        $args = null
    ) {
        $args = ($args === null)
            ? []
            : $args;

        $this->_initBaseException($message, $code, $previous);

        $this->_setCallback($callable);
        $this->_setArgs($args);
    }
}
