<?php

namespace Dhii\Factory\Exception;

use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Dhii\Factory\FactoryInterface;
use Dhii\Factory\SubjectConfigAwareTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\Container\ContainerInterface;

/**
 * Concrete implementation of an exception thrown when a factory fails to create a subject.
 *
 * @since [*next-version*]
 */
class CouldNotMakeException extends AbstractBaseFactoryException implements CouldNotMakeExceptionInterface
{
    /*
     * Provides awareness of subject factory configuration.
     *
     * @since [*next-version*]
     */
    use SubjectConfigAwareTrait;

    /*
     * Provides functionality for normalizing containers.
     *
     * @since [*next-version*]
     */
    use NormalizeContainerCapableTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null        $message  The exception message, if any.
     * @param int|null                      $code     The exception code, if any.
     * @param RootException|null            $previous The previous exception for chaining, if any.
     * @param FactoryInterface|null         $factory  The factory instance, if any.
     * @param array|ContainerInterface|null $config   The config that was given to the factory, if any.`
     */
    public function __construct(
        $message = '',
        $code = 0,
        RootException $previous = null,
        $factory = null,
        $config = null
    ) {
        $this->_initParent($message, $code, $previous);
        $this->_setFactory($factory);
        $this->_setSubjectConfig($config);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getSubjectConfig()
    {
        return $this->_getSubjectConfig();
    }
}
