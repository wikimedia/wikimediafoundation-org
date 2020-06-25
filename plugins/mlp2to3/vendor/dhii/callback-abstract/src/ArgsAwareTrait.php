<?php

namespace Dhii\Invocation;

use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Functionality for args awareness.
 *
 * @since [*next-version*]
 */
trait ArgsAwareTrait
{
    /**
     * A list of argument values.
     *
     * @since [*next-version*]
     *
     * @var array|Traversable|stdClass|null
     */
    protected $args;

    /**
     * Retrieves the args associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return array|Traversable|stdClass The args.
     */
    protected function _getArgs()
    {
        return !is_null($this->args)
            ? $this->args
            : array();
    }

    /**
     * Assigns a args to this instance.
     *
     * @since [*next-version*]
     *
     * @param array|Traversable|stdClass $args A list of argument values.
     *
     * @throws InvalidArgumentException If the arguments list is invalid.
     */
    protected function _setArgs($args)
    {
        $args = $this->_normalizeIterable($args);

        $this->args = $args;
    }

    /**
     * Translates a string, and replaces placeholders.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);
}
