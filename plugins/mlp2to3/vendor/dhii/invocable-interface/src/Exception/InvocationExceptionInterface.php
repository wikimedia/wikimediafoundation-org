<?php

namespace Dhii\Invocation\Exception;

use Dhii\Exception\ThrowableInterface;
use Dhii\Invocation\CallableAwareInterface;
use Dhii\Invocation\ArgsAwareInterface;

/**
 * An exception that occurs in relation to an invocation.
 *
 * @since [*next-version*]
 */
interface InvocationExceptionInterface extends
        ThrowableInterface,
        CallableAwareInterface,
        ArgsAwareInterface
{
}
