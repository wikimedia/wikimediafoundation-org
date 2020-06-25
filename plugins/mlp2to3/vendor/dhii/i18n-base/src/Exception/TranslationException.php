<?php

namespace Dhii\I18n\Exception;

use Dhii\I18n\TranslatorInterface;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Represents an exception related to translation.
 *
 * @since 0.1
 */
class TranslationException extends AbstractTranslationException implements TranslationExceptionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Exception::__construct()
     * @since 0.1
     *
     * @param string|Stringable|null   $subject    The subject being translated, if any.
     * @param TranslatorInterface|null $translator The translator performing the translation, if any.
     */
    public function __construct(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        $subject = null,
        TranslatorInterface $translator = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->_setSubject($subject);
        $this->_setTranslator($translator);

        $this->_construct();
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getSubject()
    {
        return $this->_getSubject();
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getTranslator()
    {
        return $this->_getTranslator();
    }
}
