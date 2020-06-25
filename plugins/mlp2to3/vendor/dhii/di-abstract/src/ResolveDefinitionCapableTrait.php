<?php

namespace Dhii\Di;

use Dhii\Exception\InternalExceptionInterface;
use Dhii\Invocation\Exception\InvocationExceptionInterface;
use InvalidArgumentException;
use RuntimeException;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use stdClass;
use Traversable;

/**
 * Functionality for resolving a service definition.
 *
 * @since [*next-version*]
 */
trait ResolveDefinitionCapableTrait
{
    /**
     * Resolves a service definition.
     *
     * Resolving a definition means acquiring an instance of a service according to its definition.
     *
     * @since [*next-version*]
     *
     * @param mixed $definition The service definition. If callable, the return value will be used.
     *
     * @throws RuntimeException If the callable cannot be invoked.
     *
     * @return mixed The resolved service.
     */
    protected function _resolveDefinition($definition)
    {
        if (!is_callable($definition)) {
            return $definition;
        }

        try {
            $args = $this->_normalizeArray($this->_getArgsForDefinition($definition));

            return $this->_invokeCallable($definition, $args);
        } catch (RootException $e) {
            throw $this->_createRuntimeException($this->__('Could not resolve definition'), null, $e);
        }
    }

    /**
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable $callable The callable to invoke.
     * @param array    $args     The arguments to invoke the callable with.
     *
     * @throws InvocationExceptionInterface If the callable cannot be invoked.
     * @throws InternalExceptionInterface   If a problem occurs during invocation.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeCallable($callable, $args);

    /**
     * Retrieves arguments for a service definition.
     *
     * @since [*next-version*]
     *
     * @return array|Traversable|stdClass The list of args.
     */
    abstract protected function _getArgsForDefinition($definition);

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
    abstract protected function __($string, $args = array(), $context = null);
}
