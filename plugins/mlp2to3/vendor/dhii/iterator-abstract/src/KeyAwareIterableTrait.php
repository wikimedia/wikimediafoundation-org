<?php

namespace Dhii\Iterator;

use Dhii\Data\KeyAwareInterface;
use Traversable;

/**
 * Common functionality for iterators that iterator over key-aware instances.
 *
 * @since [*next-version*]
 */
trait KeyAwareIterableTrait
{
    /**
     * Retrieves the key for the current element of an iterable.
     *
     * @since [*next-version*]
     *
     * @param array|Traversable $iterable The iterable.
     *
     * @return string|int|null The current key.
     */
    protected function _getCurrentIterableKey(&$iterable)
    {
        $current = $this->_getCurrentIterableValue($iterable);

        return ($current instanceof KeyAwareInterface)
            ? $current->getKey()
            : key($iterable);
    }

    /**
     * Retrieves the single path segment for a specific element.
     *
     * @since [*next-version*]
     *
     * @param string|int $key   The element key.
     * @param mixed      $value The element value.
     *
     * @return string|null The path segment string or null for no path segment.
     */
    protected function _getElementPathSegment($key, $value)
    {
        return ($value instanceof KeyAwareInterface)
            ? $value->getKey()
            : $key;
    }

    /**
     * Retrieves the value for the current element of an iterable.
     *
     * @since [*next-version*]
     *
     * @param array|Traversable $iterable The iterable.
     *
     * @return KeyAwareInterface The key-aware value.
     */
    abstract protected function _getCurrentIterableValue(&$iterable);
}
