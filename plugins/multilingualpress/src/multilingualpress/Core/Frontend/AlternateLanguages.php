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
use Inpsyde\MultilingualPress\Framework\Api\Translations;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\WordpressContext;

/**
 * Alternate languages data object.
 */
class AlternateLanguages implements \IteratorAggregate
{
    const FILTER_HREFLANG_POST_STATUS = 'multilingualpress.hreflang_post_status';
    const FILTER_HREFLANG_TRANSLATIONS = 'multilingualpress.hreflang_translations';
    const FILTER_HREFLANG_URL = 'multilingualpress.hreflang_url';

    /**
     * @var SiteSettingsRepository
     */
    protected $siteSettingsRepository;

    /**
     * @var Translations
     */
    private $api;

    /**
     * @var string[]
     */
    private $urls;

    /**
     * @param Translations $api
     */
    public function __construct(Translations $api, SiteSettingsRepository $siteSettingsRepository)
    {
        $this->api = $api;
        $this->siteSettingsRepository = $siteSettingsRepository;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \Traversable
    {
        $this->ensureUrls();

        return new \ArrayIterator($this->urls);
    }

    /**
     * Takes care that the alternate language URLs are available for use.
     */
    private function ensureUrls()
    {
        if (is_array($this->urls)) {
            return;
        }

        $urls = [];

        /**
         * Filters the allowed status for posts to be included in hreflang links.
         *
         * @param string[] $postStatus
         */
        $postStatus = (array)apply_filters(self::FILTER_HREFLANG_POST_STATUS, ['publish']);
        $currentSiteId = get_current_blog_id();

        $args = TranslationSearchArgs::forContext(new WordpressContext())
            ->forSiteId($currentSiteId)
            ->makeStrictSearch()
            ->includeBase()
            ->forPostStatus(...array_filter($postStatus, 'is_string'));

        $translations = $this->api->searchTranslations($args);

        foreach ($translations as $translation) {
            $url = $translation->remoteUrl();
            if ($url) {
                /**
                 * Filters the URL to be used for hreflang links.
                 *
                 * @param string $url
                 * @param Translation $translation
                 */
                $url = apply_filters(self::FILTER_HREFLANG_URL, $url, $translation);

                $hreflangDisplayType = $this->siteSettingsRepository->hreflangSettingForSite(
                    $translation->remoteSiteId(),
                    SiteSettingsRepository::NAME_HREFLANG_DISPLAY_TYPE
                );
                $hreflangDisplayTypeIsCountry = $hreflangDisplayType && $hreflangDisplayType === 'country';
                $language = $translation->language();

                $code = $hreflangDisplayTypeIsCountry ? $language->isoCode() : $language->bcp47tag();

                $urls[$code] = (string)$url;
            }
        }

        /**
         * Filters the available translations to be used for hreflang links.
         *
         * @param string[] $translations
         */
        $this->urls = (array)apply_filters(self::FILTER_HREFLANG_TRANSLATIONS, $urls);
    }
}
