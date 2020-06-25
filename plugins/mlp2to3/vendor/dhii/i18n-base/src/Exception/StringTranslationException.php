<?php

namespace Dhii\I18n\Exception;

use Dhii\I18n\TranslatorInterface;
use Dhii\Data\ValueAwareInterface as Value;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Represents an exception related to string translation.
 *
 * @since 0.1
 */
class StringTranslationException extends AbstractStringTranslationException implements StringTranslationExceptionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Exception::__construct()
     * @since 0.1
     *
     * @param string|Stringable|null   $string     The string being translated, if any.
     * @param TranslatorInterface|null $translator The translator performing the translation, if any.
     * @param mixed|Value              $context    The context of translation, if any.
     */
    public function __construct(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        $string = null,
        TranslatorInterface $translator = null,
        $context = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->_setSubject($string);
        $this->_setTranslator($translator);
        $this->_setContext($context);

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

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getContext()
    {
        return $this->_getContext();
    }
}
