<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Core\Frontend;

/**
 * Alternate language HTTP header renderer implementation.
 */
final class AltLanguageHttpHeaderRenderer implements AltLanguageRenderer
{
    const FILTER_HREFLANG = 'multilingualpress.hreflang_http_header';
    const FILTER_RENDER_HREFLANG = 'multilingualpress.render_hreflang';

    /**
     * @var AlternateLanguages
     */
    private $alternateLanguages;

    /**
     * @param AlternateLanguages $alternateLanguages
     */
    public function __construct(AlternateLanguages $alternateLanguages)
    {
        $this->alternateLanguages = $alternateLanguages;
    }

    /**
     * Renders all available alternate languages as Link HTTP headers.
     *
     * @param array ...$args
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function render(...$args)
    {
        // phpcs:enable

        $translations = iterator_to_array($this->alternateLanguages);

        /** This filter is documented in src/Core/FrontEnd/AlternateLanguageHTMLLinkTagRenderer.php */
        if (
            !apply_filters(
                self::FILTER_RENDER_HREFLANG,
                count($translations) > 1,
                $translations,
                $this->type()
            )
        ) {
            return;
        }

        foreach ($translations as $language => $url) {
            $header = sprintf(
                'Link: <%1$s>; rel="alternate"; hreflang="%2$s"',
                esc_url($url),
                esc_attr($language)
            );

            /**
             * Filters the output of the hreflang links in the HTTP header.
             *
             * @param string $header
             * @param string $language
             * @param string $url
             */
            $header = (string)apply_filters(self::FILTER_HREFLANG, $header, $language, $url);
            if ($header) {
                header($header, false);
            }
        }
    }

    /**
     * Returns the output type.
     *
     * @return int
     */
    public function type(): int
    {
        return self::TYPE_HTTP_HEADER;
    }
}
