<?php

namespace Dhii\Factory;

use ArrayAccess;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use stdClass;

/**
 * Functionality for storing and retrieving some factory configuration for a subject.
 *
 * @since [*next-version*]
 */
trait SubjectConfigAwareTrait
{
    /**
     * The subject factory configuration.
     *
     * @since [*next-version*]
     *
     * @var array|ArrayAccess|stdClass|ContainerInterface|null
     */
    protected $subjectConfig;

    /**
     * Retrieves the subject factory configuration associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return array|ArrayAccess|ContainerInterface|stdClass|null The subject factory configuration, if any.
     */
    protected function _getSubjectConfig()
    {
        return $this->subjectConfig;
    }

    /**
     * Sets the subject factory configuration for this instance.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|ContainerInterface|stdClass|null $subjectConfig The subject factory config, if any.
     *
     * @throws InvalidArgumentException If the argument is not a valid container.
     */
    protected function _setSubjectConfig($subjectConfig)
    {
        if ($subjectConfig === null || $subjectConfig = $this->_normalizeContainer($subjectConfig)) {
            $this->subjectConfig = $subjectConfig;
        }
    }

    /**
     * Normalizes a container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $container The container to normalize.
     *
     * @throws InvalidArgumentException If the container is invalid.
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface Something that can be used with
     *                                                       {@see ContainerGetCapableTrait#_containerGet()} or
     *                                                       {@see ContainerHasCapableTrait#_containerHas()}.
     */
    abstract protected function _normalizeContainer($container);
}
