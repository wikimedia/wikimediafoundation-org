<?php

namespace Dhii\Wp\I18n;

/**
 * Something that can have a text domain retrieved from it.
 *
 * @since [*next-version*]
 */
interface TextDomainAwareInterface
{
    /**
     * Retrieves the text domain used by this instance.
     *
     * @since [*next-version*]
     *
     * @return string The text domain.
     */
    public function getTextDomain();
}
