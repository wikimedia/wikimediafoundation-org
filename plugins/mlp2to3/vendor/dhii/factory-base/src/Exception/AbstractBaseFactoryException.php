<?php

namespace Dhii\Factory\Exception;

use Dhii\Exception\AbstractBaseException;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Factory\FactoryAwareTrait;
use Dhii\I18n\StringTranslatingTrait;

/**
 * Base functionality for an exception thrown in relation to a factory.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseFactoryException extends AbstractBaseException implements FactoryExceptionInterface
{
    /*
     * Provides awareness of a factory instance.
     *
     * @since [*next-version*]
     */
    use FactoryAwareTrait {
        _getFactory as public getFactory;
    }

    /*
     * Provides functionality for creating invalid-argument exception instances.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;
}
