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

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Language\EmbeddedLanguage;

use function Inpsyde\MultilingualPress\siteLanguageTag;

/**
 * Alternate language HTML link tag renderer implementation.
 */
final class AltLanguageHtmlLinkTagRenderer implements AltLanguageRenderer
{
    const FILTER_HREFLANG = 'multilingualpress.hreflang_html_link_tag';
    const FILTER_RENDER_HREFLANG = 'multilingualpress.render_hreflang';

    const KSES_TAGS = [
        'link' => [
            'href' => true,
            'hreflang' => true,
            'rel' => true,
        ],
    ];

    /**
     * @var AlternateLanguages
     */
    private $alternateLanguages;

    /**
     * @var SiteSettingsRepository
     */
    private $siteSettingsRepository;

    /**
     * @param AlternateLanguages $alternateLanguages
     * @param SiteSettingsRepository $siteSettingsRepository
     */
    public function __construct(
        AlternateLanguages $alternateLanguages,
        SiteSettingsRepository $siteSettingsRepository
    ) {

        $this->alternateLanguages = $alternateLanguages;
        $this->siteSettingsRepository = $siteSettingsRepository;
    }

    /**
     * Renders all alternate languages as HTML link tags into the HTML head.
     *
     * @param array ...$args
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     * phpcs:disable WordPressVIPMinimum.Security.ProperEscapingFunction.notAttrEscAttr
     */
    public function render(...$args)
    {
        // phpcs:enable

        $translations = iterator_to_array($this->alternateLanguages);
        $xDefaultLanguage = $this->xDefaultUrl($translations);

        /**
         * Filters if the hreflang links should be rendered.
         *
         * @param bool $render
         * @param string[] $translations
         * @param string $xDefault
         * @param int $type
         */
        if (
            !apply_filters(
                self::FILTER_RENDER_HREFLANG,
                count($translations) > 1 || $xDefaultLanguage,
                $translations,
                $xDefaultLanguage,
                $this->type()
            )
        ) {
            return;
        }

        foreach ($translations as $language => $url) {
            $language = EmbeddedLanguage::changeLanguageVariant($language);
            $htmlLinkTag = sprintf(
                '<link rel="alternate" hreflang="%1$s" href="%2$s">',
                esc_html($language),
                esc_url($url)
            );

            /**
             * Filters the output of the hreflang links in the HTML head.
             *
             * @param string $htmlLinkTag
             * @param string $language
             * @param string $url
             */
            $htmlLinkTag = (string)apply_filters(
                self::FILTER_HREFLANG,
                $htmlLinkTag,
                $language,
                $url
            );

            echo wp_kses($htmlLinkTag, self::KSES_TAGS);
        }

        $xDefaultUrl = $translations[$xDefaultLanguage] ?? '';
        if ($xDefaultUrl) {
            printf('<link rel="alternate" href="%s" hreflang="x-default">', esc_url($xDefaultUrl));
        }
    }

    /**
     * Returns the output type.
     *
     * @return int
     */
    public function type(): int
    {
        return self::TYPE_HTML_LINK_TAG;
    }

    /**
     * @param array $alternateLanguages
     * @return string
     */
    private function xDefaultUrl(array $alternateLanguages): string
    {
        $xDefaultLanguage = '';
        $xDefaultSetting = $this->siteSettingsRepository->allSitesSetting(
            SiteSettingsRepository::NAME_XDEFAULT
        );
        $xDefaultSiteId = (int)($xDefaultSetting[get_current_blog_id()] ?? 0);

        if (!$xDefaultSiteId) {
            return $xDefaultLanguage;
        }

        $languageTag = siteLanguageTag($xDefaultSiteId);
        foreach ($alternateLanguages as $key => $value) {
            if ($key === $languageTag) {
                $xDefaultLanguage = $languageTag;
                break;
            }
        }

        return $xDefaultLanguage;
    }
}
