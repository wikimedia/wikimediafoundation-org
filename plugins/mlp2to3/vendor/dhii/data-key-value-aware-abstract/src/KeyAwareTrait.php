<?php

namespace Dhii\Data;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

/**
 * Something that has a key.
 *
 * @since [*next-version*]
 */
trait KeyAwareTrait
{
    /**
     * The key.
     *
     * @since [*next-version*]
     *
     * @var string|Stringable|null
     */
    protected $key;

    /**
     * Retrieves the key.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|null The key.
     */
    protected function _getKey()
    {
        return $this->key;
    }

    /**
     * Sets the key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable|null $key The key. Stringable objects will be stored as is;
     *                                                   everything else wll be normalized to string.
     *
     * @throws InvalidArgumentException If key is not {@link Stringable} and could not be converted to string.
     */
    protected function _setKey($key)
    {
        if (!($key instanceof Stringable) && !is_null($key)) {
            $key = $this->_normalizeString($key);
        }

        $this->key = $key;
    }

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);
}
