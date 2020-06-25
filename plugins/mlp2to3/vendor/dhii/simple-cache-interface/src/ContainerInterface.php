<?php

namespace Dhii\Cache;

use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface as BaseContainerInterface;

interface ContainerInterface extends BaseContainerInterface
{
    /**
     * Retrieves the value for a key, falling back to default.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable          $key     The key to retrieve the value for/
     * @param mixed|callable|null        $default What to return if the value for the specified key is not found.
     *                                            If callable is passed, it will be invoked and the result will be returned.
     * @param null|int|string|Stringable $ttl     The number of seconds, for which the cache remains valid.
     *                                            If null, the underlying implementation is free to determine the lifetime, and it becomes unpredictable -
     *                                            possibly infinite.
     *
     * @return mixed The data at the specified key.
     */
    public function get($key, $default = null, $ttl = null);
}
