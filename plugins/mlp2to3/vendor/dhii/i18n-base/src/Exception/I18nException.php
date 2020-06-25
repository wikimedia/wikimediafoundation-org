<?php

namespace Dhii\I18n\Exception;

/**
 * Represents an exception related to internationalization.
 *
 * @since 0.1
 */
class I18nException extends AbstractI18nException implements I18nExceptionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Exception::__construct()
     * @since 0.1
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->_construct();
    }
}
