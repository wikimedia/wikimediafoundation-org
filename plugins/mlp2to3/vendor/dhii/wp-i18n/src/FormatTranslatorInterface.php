<?php

namespace Dhii\Wp\I18n;

use Dhii\I18n\FormatTranslatorInterface as BaseFormatTranslator;

/**
 * Something that can represent a WordPress translator.
 *
 * @since [*next-version*]
 */
interface FormatTranslatorInterface extends
    BaseFormatTranslator,
    TextDomainAwareInterface
{
}
