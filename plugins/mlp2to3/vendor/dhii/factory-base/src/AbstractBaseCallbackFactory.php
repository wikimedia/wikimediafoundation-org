<?php

namespace Dhii\Factory;

use ArrayAccess;
use Dhii\Exception\CreateInternalExceptionCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Factory\Exception\CreateCouldNotMakeExceptionCapableTrait;
use Dhii\Factory\Exception\CreateFactoryExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Invocation\CreateInvocationExceptionCapableTrait;
use Dhii\Invocation\CreateReflectionForCallableCapableTrait;
use Dhii\Invocation\Exception\InvocationExceptionInterface;
use Dhii\Invocation\InvokeCallableCapableTrait;
use Dhii\Invocation\NormalizeCallableCapableTrait;
use Dhii\Invocation\NormalizeMethodCallableCapableTrait;
use Dhii\Invocation\ValidateParamsCapableTrait;
use Dhii\Iterator\CountIterableCapableTrait;
use Dhii\Iterator\ResolveIteratorCapableTrait;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Validation\CreateValidationFailedExceptionCapableTrait;
use Dhii\Validation\GetArgsListErrorsCapableTrait;
use Dhii\Validation\GetValueTypeErrorCapableTrait;
use Exception as RootException;
use Psr\Container\ContainerInterface;
use ReflectionFunction;
use ReflectionMethod;
use stdClass;

/**
 * A concrete implementation of a factory that uses a callback to create subject instances.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseCallbackFactory implements FactoryInterface
{
    /*
     * Provides functionality for invoking callable things.
     *
     * @since [*next-version*]
     */
    use InvokeCallableCapableTrait;

    /*
     * Provides param validation functionality.
     *
     * @since [*next-version*]]
     */
    use ValidateParamsCapableTrait;

    /*
     * Provides functionality for counting iterable things.
     *
     * @since [*next-version*]
     */
    use CountIterableCapableTrait;

    /*
     * Provides functionality for retrieving arg list errors
     *
     * @since [*next-version*]
     */
    use GetArgsListErrorsCapableTrait;

    /*
     * Provides functionality for retrieving value type errors.
     *
     * @since [*next-version*]
     */
    use GetValueTypeErrorCapableTrait;

    /*
     * Provides iterator resolution functionality.
     *
     * @since [*next-version*]
     */
    use ResolveIteratorCapableTrait;

    /*
     * Provides integer normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /*
     * Provides string normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /*
     * Provides functionality for normalizing arrays.
     *
     * @since [*next-version*]
     */
    use NormalizeArrayCapableTrait;

    /*
     * Provides functionality for normalizing callable things.
     *
     * @since [*next-version*]
     */
    use NormalizeCallableCapableTrait;

    /*
     * Provides functionality for normalizing callable methods.
     *
     * @since [*next-version*]
     */
    use NormalizeMethodCallableCapableTrait;

    /*
     * Provides functionality for normalizing iterable things.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /*
     * Provides functionality for creating reflections for callable things.
     *
     * @since [*next-version*]
     */
    use CreateReflectionForCallableCapableTrait;

    /*
     * Provides functionality for creating invalid-argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Provides functionality for creating out of range exceptions.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /*
     * Provides functionality for creating invocation exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvocationExceptionCapableTrait;

    /*
     * Provides functionality for creating factory exceptions.
     *
     * @since [*next-version*]
     */
    use CreateFactoryExceptionCapableTrait;

    /*
     * Provides functionality for creating could-not-make exceptions.
     *
     * @since [*next-version*]
     */
    use CreateCouldNotMakeExceptionCapableTrait;

    /*
     * Provides functionality for creating validation failure exceptions.
     *
     * @since [*next-version*]
     */
    use CreateValidationFailedExceptionCapableTrait;

    /*
     * Provides functionality for creating internal exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInternalExceptionCapableTrait;

    /*
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function make($config = null)
    {
        try {
            return $this->_invokeCallable($this->_getFactoryCallback($config), [$config]);
        } catch (InvocationExceptionInterface $invocationException) {
            throw $this->_createCouldNotMakeException(
                $this->__('Could not make subject instance'),
                null,
                $invocationException,
                $this,
                $config
            );
        } catch (RootException $exception) {
            throw $this->_createFactoryException(
                $this->__('An error occurred while trying to make the subject instance'),
                null,
                $exception,
                $this
            );
        }
    }

    /**
     * Retrieves the factory callback.
     *
     * The factory callback is the callable that will be invoked to create a subject instance.
     * This callback will receive the subject config as the first argument.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface|null $config The subject config, if any.
     *
     * @return callable The callable to invoke.
     */
    abstract protected function _getFactoryCallback($config = null);

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createReflectionMethod($className, $methodName)
    {
        return new ReflectionMethod($className, $methodName);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createReflectionFunction($functionName)
    {
        return new ReflectionFunction($functionName);
    }
}
