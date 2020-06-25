<?php

namespace Dhii\Data;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

/**
 * Functionality for setting and retrieving a name.
 *
 * @since [*next-version*]
 */
trait NameAwareTrait
{
    /**
     * The name.
     *
     * @since [*next-version*]
     *
     * @var string|Stringable|null
     */
    protected $name;

    /**
     * Retrieves the name.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|null The name.
     */
    protected function _getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable|null $name The name. Stringable objects will be stored as is;
     *                                                    everything else wll be normalized to string.
     *
     * @throws InvalidArgumentException If name is not {@link Stringable} and could not be converted to string.
     */
    protected function _setName($name)
    {
        if (!($name instanceof Stringable) && !is_null($name)) {
            $name = $this->_normalizeString($name);
        }

        $this->name = $name;
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
