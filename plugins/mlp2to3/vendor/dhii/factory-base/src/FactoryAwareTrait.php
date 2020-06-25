<?php

namespace Dhii\Factory;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for storing and retrieving a factory instance.
 *
 * @since [*next-version*]
 */
trait FactoryAwareTrait
{
    /**
     * The factory associated with this instance.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface|null
     */
    protected $factory;

    /**
     * Retrieves the factory associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface|null The factory instance, if any.
     */
    protected function _getFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the factory for this instance.
     *
     * @since [*next-version*]
     *
     * @param FactoryInterface|null $factory The factory instance to set, if any.
     */
    protected function _setFactory($factory)
    {
        if ($factory !== null && !($factory instanceof FactoryInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a valid factory instance'),
                null,
                null,
                $factory
            );
        }
        $this->factory = $factory;
    }

    /**
     * Creates a new invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The invalid argument, if any.
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
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     * @see   _translate()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
