<?php

namespace Dhii\Iterator;

use Dhii\Util\String\StringableInterface as Stringable;
use stdClass;
use Traversable;
use Exception as RootException;
use InvalidArgumentException;

trait PathSegmentsAwareTrait
{
    /**
     * A list of path segments.
     *
     * @since [*next-version*]
     *
     * @var string[]|Stringable[]|Traversable
     */
    protected $pathSegments;

    /**
     * Sets the path segments.
     *
     * @since [*next-version*]
     *
     * @param string[]|Stringable[]|Traversable|stdClass $segments A list of path segments.
     */
    protected function _setPathSegments($segments)
    {
        /*
         * `PathSegmentsAwareInterface#getPathSegments()` disallows `stdClass`.
         */
        if ($segments instanceof stdClass) {
            $segments = (array) $segments;
        }

        $segments = $this->_normalizeIterable($segments);

        $this->pathSegments = $segments;
    }

    /**
     * Retrieves the path segments.
     *
     * @since [*next-version*]
     *
     * @return string[]|Stringable[]|Traversable The list of segments.
     */
    protected function _getPathSegments()
    {
        return is_null($this->pathSegments)
            ? []
            : $this->pathSegments;
    }

    /**
     * Creates a new Invalid Argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);
}
