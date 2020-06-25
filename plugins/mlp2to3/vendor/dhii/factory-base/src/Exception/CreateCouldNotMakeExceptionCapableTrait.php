<?php

namespace Dhii\Factory\Exception;

use ArrayAccess;
use Dhii\Factory\FactoryInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\Container\ContainerInterface;
use stdClass;

/**
 * Functionality for creating could-not-make exception instances.
 *
 * @since [*next-version*]
 */
trait CreateCouldNotMakeExceptionCapableTrait
{
    /**
     * Creates a new factory exception instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null                        $message  The exception message, if any.
     * @param string|Stringable|null                        $code     The exception code, if any.
     * @param RootException|null                            $previous The previous exception for chaining, if any.
     * @param FactoryInterface|null                         $factory  The factory instance, if any.
     * @param array|ArrayAccess|stdClass|ContainerInterface $config   The subject config, if any.
     *
     * @return CouldNotMakeExceptionInterface The created exception instance.
     */
    protected function _createCouldNotMakeException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $factory = null,
        $config = null
    ) {
        return new CouldNotMakeException($message, $code, $previous, $factory, $config);
    }
}
