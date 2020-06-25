<?php

namespace Dhii\Wp\I18n;

use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Data\ValueAwareInterface as Value;

/**
 * Translates format strings using WP functionality.
 *
 * @since [*next-version*]
 */
class FormatTranslator extends AbstractFormatTranslator implements FormatTranslatorInterface
{
    /**
     * @since [*next-version*]
     *
     * @param string|Stringable|Value $textDomain The text domain that this instance will use.
     */
    public function __construct($textDomain)
    {
        $this->_setTextDomain($textDomain);

        $this->_construct();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function translate($format, $params = null, $context = null)
    {
        return $this->_translate($format, $context, $params);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return string The text domain.
     */
    public function getTextDomain()
    {
        return $this->_getTextDomain();
    }
}
