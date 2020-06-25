<?php

namespace Dhii\Collection;

use Dhii\Factory\Exception\CreateCouldNotMakeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Exception as RootException;

/**
 * Common functionality for map factories that can convert a hierarchy of iterables into a hierarchy of maps.
 *
 * @since [*next-version*]
 */
abstract class AbstractRecursiveMapFactory implements MapFactoryInterface
{
    /* @since [*next-version*] */
    use MakeCapableMapTrait;

    /* @since [*next-version*] */
    use RecursiveFactoryTrait;

    /* @since [*next-version*] */
    use CreateCouldNotMakeExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function make($config = null)
    {
        try {
            return $this->_make($config);
        } catch (RootException $e) {
            throw $this->_createCouldNotMakeException($this->__('Could not create map'), null, $e, $this, $config);
        }
    }
}
