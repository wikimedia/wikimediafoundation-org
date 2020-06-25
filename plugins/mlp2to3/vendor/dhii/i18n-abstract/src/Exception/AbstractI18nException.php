<?php

namespace Dhii\I18n\Exception;

/**
 * Common functionality for internationalization exceptions.
 *
 * @since 0.1
 */
abstract class AbstractI18nException extends \Exception
{
    /**
     * Parameter-less constructor.
     *
     * Invoke this in the actual constructor.
     *
     * @since 0.1
     */
    protected function _construct()
    {
    }
}
