<?php

namespace Dhii\I18n\Exception;

use Dhii\I18n\TranslatorInterface;

/**
 * Common functionality for translation exceptions.
 *
 * @since 0.1
 */
abstract class AbstractTranslationException extends AbstractI18nException
{
    /**
     * The translator performing the translation.
     *
     * @since 0.1
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * The subject being translated.
     *
     * @since 0.1
     *
     * @var mixed
     */
    protected $subject;

    /**
     * Associates a translator with this instance.
     *
     * @since 0.1
     *
     * @param TranslatorInterface $translator
     *
     * @return $this This instance.
     */
    protected function _setTranslator(TranslatorInterface $translator = null)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Retrieves the translator associated with this instance.
     *
     * @since 0.1
     *
     * @return TranslatorInterface|null The translator, if any.
     */
    protected function _getTranslator()
    {
        return $this->translator;
    }

    /**
     * Associates a translation subject with this instance.
     *
     * @since 0.1
     *
     * @param mixed $subject
     *
     * @return $this This instance.
     */
    protected function _setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Retrieves the translation subject associated with this instance.
     *
     * @since 0.1
     *
     * @return mixed|null The translation subject, if any.
     */
    protected function _getSubject()
    {
        return $this->subject;
    }
}
