<?php

namespace Dhii\Data\Object;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

/**
 * Functionality for array key normalization.
 *
 * @since [*next-version*]
 */
trait NormalizeKeyCapableTrait
{
    /**
     * Normalizes an array key.
     *
     * If key is not an integer (strict type check), it will be normalized to string.
     * Otherwise it is left as is.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key to normalize.
     *
     * @throws InvalidArgumentException If key cannot be normalized.
     *
     * @return string|int The normalized key.
     */
    protected function _normalizeKey($key)
    {
        if (!is_int($key)) {
            $key = $this->_normalizeString($key);
        }

        return $key;
    }

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);
}
