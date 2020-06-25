<?php

namespace Dhii\Wp\I18n;

use Dhii\I18n\AbstractBaseFormatTranslator;
use Dhii\Data\ValueAwareInterface as Value;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Common functionality for WordPress format translators.
 *
 * @since [*next-version*]
 */
abstract class AbstractFormatTranslator extends AbstractBaseFormatTranslator
{
    /**
     * The text domain that will be used if no other text domain provided.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    const DEFAULT_TEXT_DOMAIN = 'default';

    /**
     * The text domain used by this instance for translation.
     *
     * @since [*next-version*]
     *
     * @var string|Stringable;
     */
    protected $textDomain;

    /**
     * Parameter-less constructor.
     *
     * @todo Remove when dhii/i18n-abstract#2 is ready.
     *
     * @since [*next-version*]
     */
    protected function _construct()
    {
    }

    /**
     * Translates a string.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|Value $string  The string to translate.
     * @param string|Stringable|Value $context The context of translation, if any.
     *                                         If no context is specified, it will not be used for translation at all.
     *
     * @return string The translated string.
     */
    protected function _translateString($string, $context = null)
    {
        $domain = $this->_getTextDomain();
        $result = is_null($context)
                ? $this->_translateWithoutContext($string, $domain)
                : $this->_translateWithContext($string, $context, $domain);

        return $result;
    }

    /**
     * Translate a string with context.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|Value $subject The string to translate.
     * @param string|Stringable|Value $context The context of translation.
     * @param string|Stringable|Value $domain  The text domain of the string.
     *                                         If null, the default text domain will be used. See {@see DEFAULT_TEXT_DOMAIN}.
     *
     * @return string The translated string.
     */
    protected function _translateWithContext($subject, $context, $domain = null)
    {
        if (is_null($domain)) {
            $domain = static::DEFAULT_TEXT_DOMAIN;
        }

        $domain  = $this->_resolveTextDomain($domain);
        $context = $this->_resolveContext($context);
        $subject = $this->_resolveSubject($subject);

        return _x($subject, $context, $domain);
    }

    /**
     * Translate a string without context.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|Value $subject The subject to translate.
     * @param string|Stringable|Value $domain  The text domain of the string.
     *                                         If null, the default text domain will be used. See {@see DEFAULT_TEXT_DOMAIN}.
     *
     * @return string The translated string.
     */
    protected function _translateWithoutContext($subject, $domain = null)
    {
        if (is_null($domain)) {
            $domain = static::DEFAULT_TEXT_DOMAIN;
        }

        $domain  = $this->_resolveTextDomain($domain);
        $subject = $this->_resolveSubject($subject);

        return __($subject, $domain);
    }

    /**
     * Associate a text domain with this instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|Value $domain The text domain.
     *
     * @return $this This instance.
     */
    protected function _setTextDomain($domain)
    {
        $this->textDomain = $domain;

        return $this;
    }

    /**
     * Retrieves the text domain associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|Value The text domain.
     */
    protected function _getTextDomain()
    {
        return $this->textDomain;
    }

    /**
     * Converts a text domain representation into its string value.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|Value $domain The text domain.
     *
     * @return string The text domain name.
     */
    protected function _resolveTextDomain($domain)
    {
        return $this->_resolveString($domain);
    }

    /**
     * Converts a context representation into its normalized value.
     *
     * @since [*next-version*]
     *
     * @param mixed|Stringable|Value $context The context.
     *
     * @return mixed The resolved value.
     */
    protected function _resolveContext($context)
    {
        return $this->_resolveString($context);
    }

    /**
     * Converts a translation subject to a simple version.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|Value $subject The subject to resolve.
     *
     * @return string The resolved subject.
     */
    protected function _resolveSubject($subject)
    {
        return $this->_resolveString($subject);
    }

    /**
     * Converts a value to a simple version.
     *
     * @since [*next-version*]
     *
     * @param mixed|Value $value The value to resolve.
     *
     * @return mixed The simpler value.
     */
    protected function _resolveValue($value)
    {
        if ($value instanceof Value) {
            $value = $value->getValue();
        }

        return $value;
    }

    /**
     * Converts a string representation into its primitive value.
     *
     * @since [*next-version*]
     *
     * @param mixed|Stringable|Value $string The string representation.
     * 
     * @return string The string value.
     */
    protected function _resolveString($string)
    {
        if ($string instanceof Stringable) {
            return (string) $string;
        }

        $string = $this->_resolveValue($string);

        return (string) $string;
    }
}
