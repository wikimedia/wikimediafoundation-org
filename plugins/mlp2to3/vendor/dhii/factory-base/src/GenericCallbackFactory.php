<?php

namespace Dhii\Factory;

use Dhii\Invocation\CallbackAwareTrait;

/**
 * A generic factory that is aware of a callback that will be invoked for creating the new instance.
 *
 * @since [*next-version*]
 */
class GenericCallbackFactory extends AbstractBaseCallbackFactory
{
    /*
     * Provides callback awareness.
     *
     * @since [*next-version*]
     */
    use CallbackAwareTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param callable $callback The callback.
     */
    public function __construct($callback)
    {
        $this->_setCallback($callback);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getFactoryCallback($config = null)
    {
        return $this->_getCallback();
    }
}
