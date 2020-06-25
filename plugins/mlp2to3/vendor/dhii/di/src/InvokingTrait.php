<?php

namespace Dhii\Di;

use Dhii\Invocation\InvokeCallableCapableTrait;
use Dhii\Invocation\NormalizeCallableCapableTrait;
use Dhii\Invocation\NormalizeMethodCallableCapableTrait;
use Dhii\Invocation\ValidateParamsCapableTrait;
use Dhii\Validation\GetArgsListErrorsCapableTrait;
use Dhii\Validation\GetValueTypeErrorCapableTrait;

/**
 * Aggregates methods necessary for callable invocation.
 *
 * @since [*next-version*]
 */
trait InvokingTrait
{
    /* Ability to invoke a callable.
     *
     * @since [*next-version*]
     */
    use InvokeCallableCapableTrait;

    /* Ability to validate callable params.
     *
     * @since [*next-version*]
     */
    use ValidateParamsCapableTrait;

    /* Ability to retrieve argument list validation errors.
     *
     * @since [*next-version*]
     */
    use GetArgsListErrorsCapableTrait;

    /* Ability to retrieve a value type validation error.
     *
     * @since [*next-version*]
     */
    use GetValueTypeErrorCapableTrait;

    /* Ability to normalize a callable;
     *
     * @since [*next-version*]
     */
    use NormalizeCallableCapableTrait;

    /* Ability to normalize a callable that invokes a method.
     *
     * @since [*next-version*]
     */
    use NormalizeMethodCallableCapableTrait;
}
