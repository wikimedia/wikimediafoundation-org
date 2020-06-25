<?php

namespace Dhii\I18n\Exception;

use Dhii\I18n\TranslatorInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Data\ValueAwareInterface as Value;

/**
 * Represents an exception related to string translation.
 *
 * @since 0.1
 */
class FormatTranslationException extends AbstractFormatTranslationException implements FormatTranslationExceptionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Exception::__construct()
     * @since 0.1
     *
     * @param string|Stringable|null   $format     The format string being translated, if any.
     * @param TranslatorInterface|null $translator The translator performing the translation, if any.
     * @param mixed|Value              $context    The context of translation, if any.
     * @param array|null               $params     The interpolation parameters used, if any.
     */
    public function __construct(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        $format = null,
        TranslatorInterface $translator = null,
        $context = null,
        $params = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->_setSubject($format);
        $this->_setTranslator($translator);
        $this->_setContext($context);
        $this->_setInterpolationParams($params);

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

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getParams()
    {
        return $this->_getInterpolationParams();
    }
}
