<?php

namespace Dhii\Di;

use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Data\Object\DataStoreAwareContainerTrait;
use Dhii\Data\Object\GetDataCapableTrait;
use Dhii\Data\Object\HasDataCapableTrait;

/**
 * Aggregates methods for a readable data object.
 *
 * @since [*next-version*]
 */
trait DataObjectTrait
{
    /* Awareness of an internal data store.
     *
     * @since [*next-version*]
     */
    use DataStoreAwareContainerTrait;

    /* Ability to check for data by key.
     *
     * @since [*next-version*]
     */
    use HasDataCapableTrait;

    /* Ability to retrieve data by key.
     *
     * @since [*next-version*]
     */
    use GetDataCapableTrait;

    /* Ability to retrieve data from a container.
     *
     * @since [*next-version*]
     */
    use ContainerGetCapableTrait;

    /* Ability to check for data on a container.
     *
     * @since [*next-version*]
     */
    use ContainerHasCapableTrait;

    /* Ability to normalize a container key.
     *
     * @since [*next-version*]
     */
    use NormalizeKeyCapableTrait;
}
