<?php

namespace Dhii\Cache;

use Dhii\Invocation\Exception\InvocationExceptionInterface;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use RuntimeException;
use stdClass;
use Traversable;
use Exception as RootException;

/**
 * Functionality for getting a cached member.
 *
 * @since [*next-version*]
 */
trait GetCachedCapableTrait
{
    /**
     * Retrieves a cached value by key, generating it if it does not exist.
     *
     * This implementation does not support TTL. Values will remain valid for the lifetime of this
     * instance, or until removed.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key     The key to retrieve the value for.
     * @param null|callable|mixed              $default The value to return if key isn't found. If this is a callable,
     *                                                  it will be invoked and the result will be used as default.
     * @param null|null|int|string|string      $ttl     The number of seconds for the value to be considered valid.
     *                                                  If null, the behaviour is undefined, and the value may live indefinitely.
     *
     * @throws InvalidArgumentException    If creator is not a callable, or TTL is invalid.
     * @throws OutOfRangeException         If the key is invalid.
     * @throws ContainerExceptionInterface If problem reading or writing the value.
     * @throws RuntimeException            If value could not be generated.
     *
     * @return mixed The cached value.
     */
    protected function _getCached($key, $default = null, $ttl = null)
    {
        try {
            return $this->_get($key);
        } catch (NotFoundExceptionInterface $e) {
            if (is_callable($default)) {
                try {
                    $args    = $this->_normalizeArray($this->_getGeneratorArgs($key, $default, $ttl));
                    $default = $this->_invokeCallable($default, $args);
                } catch (RootException $e) {
                    throw $this->_createRuntimeException($this->__('Could not generate value'), null, $e);
                }
            }

            $this->_set($key, $default, $ttl);

            return $default;
        }
    }

    /**
     * Retrieves a list of arguments to pass.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key       The key, for which an entry is being created.
     * @param callable                         $generator The creator, for which args are requested.
     * @param null|int|string|string           $ttl       The number of seconds for the value that is being created.
     *
     * @return array|Traversable|stdClass The list of arguments
     */
    abstract protected function _getGeneratorArgs($key, $generator, $ttl);

    /**
     * Gets a value from this container by key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to get the data.
     *
     * @throws NotFoundExceptionInterface  If the key was not found in the container.
     * @throws ContainerExceptionInterface If data could not be retrieved from the container.
     *
     * @return mixed The value for the specified key.
     */
    abstract protected function _get($key);

    /**
     * Sets a value for the specified key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key   The key to set the value for.
     * @param mixed                            $value The value to set.
     * @param null|string|Stringable|int       $ttl   The maximal number of seconds, for which the value is considered valid.
     *                                                If null, the TTL is unpredictable, perhaps indefinite.
     *
     * @throws ContainerExceptionInterface If the value could not be set.
     * @throws InvalidArgumentException    If the key or the TTL is invalid.
     */
    abstract protected function _set($key, $value, $ttl = null);

    /**
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable                   $callable The callable to invoke.
     * @param array|Traversable|stdClass $args     The arguments to invoke the callable with.
     *
     * @throws InvalidArgumentException     If the callable is not callable.
     * @throws InvalidArgumentException     if the args are not a valid list.
     * @throws InvocationExceptionInterface For errors that happen during invocation.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeCallable($callable, $args);

    /**
     * Normalizes a value into an array.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $value The value to normalize.
     *
     * @throws InvalidArgumentException If value cannot be normalized.
     *
     * @return array The normalized value.
     */
    abstract protected function _normalizeArray($value);

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);

    /**
     * Creates a new Runtime exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return RuntimeException The new exception.
     */
    abstract protected function _createRuntimeException($message = null, $code = null, $previous = null);
}
