<?php

namespace Dhii\Factory\Exception;

use Dhii\Factory\FactoryInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Concrete implementation of an exception thrown in relation to a factory.
 *
 * @since [*next-version*]
 */
class FactoryException extends AbstractBaseFactoryException
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The exception message, if any.
     * @param int|null               $code     The exception code, if any.
     * @param RootException|null     $previous The previous exception for chaining, if any.
     * @param FactoryInterface|null  $factory  The factory instance, if any.
     */
    public function __construct($message = '', $code = 0, RootException $previous = null, $factory = null)
    {
        $this->_initParent($message, $code, $previous);
        $this->_setFactory($factory);
    }
}
