<?php

namespace Dhii\Data;

/**
 * Something that has a value.
 *
 * @since [*next-version*]
 */
trait ValueAwareTrait
{
    /**
     * The value.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $value;

    /**
     * Retrieves the value.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value.
     *
     * @since [*next-version*]
     *
     * @param string $value The value.
     */
    protected function _setValue($value)
    {
        $this->value = $value;
    }
}
