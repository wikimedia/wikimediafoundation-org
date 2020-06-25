<?php

namespace Dhii\I18n;

use Dhii\I18n\Exception\I18nExceptionInterface;
use Dhii\I18n\Exception\TranslationExceptionInterface;

/**
 * Common functionality for translators.
 * 
 * @since 0.1
 */
abstract class AbstractTranslator
{
    /**
     * Translates a subject.
     *
     * @since 0.1
     *
     * @param mixed $subject The subject to translate.
     *
     * @throws TranslationExceptionInterface If could not translate string.
     * @throws I18nExceptionInterface        If a problem occurs that is not directly related to the translation process.
     *
     * @return mixed The translated subject.
     */
    abstract protected function _translate($subject);

    /**
     * Creates a new instance of an internationalization exception.
     *
     * @since 0.1
     * @see \Exception::__construct()
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     *
     * @return I18nExceptionInterface The new exception.
     */
    abstract protected function _createI18nException($message, $code = 0, \Exception $previous = null);

    /**
     * Creates a new instance of a translation exception.
     *
     * @since 0.1
     * @see \Exception::__construct()
     *
     * @param string              $message
     * @param int                 $code
     * @param \Exception          $previous
     * @param mixed               $subject    The subject which is being translated, if any.
     * @param TranslatorInterface $translator The translator which is performing the translation, if any.
     *
     * @return TranslationExceptionInterface The new exception.
     */
    abstract protected function _createTranslationException($message, $code = 0, \Exception $previous = null, $subject = null, TranslatorInterface $translator = null);
}
