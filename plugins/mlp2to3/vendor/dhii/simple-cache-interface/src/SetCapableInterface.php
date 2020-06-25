<?php

namespace Dhii\Cache;

use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Data\Container\SetCapableInterface as BaseSetCapableInterface;

/**
 * Exposes means for setting a cached value.
 *
 * @since [*next-version*]
 */
interface SetCapableInterface extends BaseSetCapableInterface
{
    /**
     * Sets a value to be cached for a period of time.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key   The key of the data member to set the value for.
     * @param mixed                            $value The value to set.
     * @param null|int|string|Stringable       $ttl   The amount of seconds, for which the value will be considered valid.
     */
    public function set($key, $value, $ttl = null);
}
