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

namespace Inpsyde\MultilingualPress\Api;

use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\Languages;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Api\Translations as FrameworkTranslations;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;
use Inpsyde\MultilingualPress\Framework\Language\Language;
use Inpsyde\MultilingualPress\Framework\Translator\Translator;
use Inpsyde\MultilingualPress\Framework\WordpressContext;
use Inpsyde\MultilingualPress\Framework\Translator\NullTranslator;

use function Inpsyde\MultilingualPress\siteExists;
use function is_array;

/**
 * Translations API implementation.
 */
final class Translations implements FrameworkTranslations
{
    const FILTER_SEARCH_TRANSLATIONS = 'multilingualpress.search_translations';

    /**
     * @var string
     */
    const SEARCH_CACHE_KEY = 'translations';

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var NullTranslator
     */
    private $nullTranslator;

    /**
     * @var WordpressContext
     */
    private $wordpressContext;

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * @var Translator[]
     */
    private $translators = [];

    /**
     * @var Facade
     */
    private $cache;

    /**
     * @var CacheSettingsRepository
     */
    private $cacheSettingsRepository;

    /**
     * @param SiteRelations $siteRelations
     * @param ContentRelations $contentRelations
     * @param Languages $languages
     * @param WordpressContext $wordpressContext
     * @param Facade $cache
     * @param CacheSettingsRepository $cacheSettingsRepository
     */
    public function __construct(
        SiteRelations $siteRelations,
        ContentRelations $contentRelations,
        Languages $languages,
        WordpressContext $wordpressContext,
        Facade $cache,
        CacheSettingsRepository $cacheSettingsRepository
    ) {

        $this->siteRelations = $siteRelations;
        $this->contentRelations = $contentRelations;
        $this->languages = $languages;
        $this->wordpressContext = $wordpressContext;
        $this->cache = $cache;
        $this->cacheSettingsRepository = $cacheSettingsRepository;
    }

    /**
     * @inheritdoc
     */
    public function searchTranslations(TranslationSearchArgs $args): array
    {
        $allowedCaching = $this->cacheSettingsRepository->get(
            CacheSettingsOptions::OPTION_GROUP_API_NAME,
            CacheSettingsOptions::OPTION_SEARCH_TRANSLATIONS_API_NAME
        );
        if ($allowedCaching) {
            $cached = $this->cache->claim(self::SEARCH_CACHE_KEY, $args->toArray());
            if ($cached && is_array($cached)) {
                return $cached;
            }
        }

        $translations = $this->buildTranslations($args);

        /**
         * Filter the translations search before they are used.
         *
         * @param Translation[] $translations
         * @param array $args
         */
        $filtered = apply_filters(self::FILTER_SEARCH_TRANSLATIONS, $translations, $args);

        if (!is_array($filtered)) {
            return [];
        }

        $result = [];
        foreach ($filtered as $siteId => $translation) {
            if (is_int($siteId) && $translation instanceof Translation && siteExists($siteId)) {
                $result[$siteId] = $translation;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function registerTranslator(Translator $translator, string $type): bool
    {
        if (isset($this->translators[$type])) {
            return false;
        }

        $this->translators[$type] = $translator;

        return true;
    }

    /**
     * @param TranslationSearchArgs $args
     * @return Translation[]
     */
    private function buildTranslations(TranslationSearchArgs $args): array
    {
        $siteId = $args->siteId();
        if ((int)$siteId < 1) {
            return [];
        }

        $contentId = $args->contentId();
        $hasRelatedContent = is_int($contentId) && $contentId > 0;
        $type = $args->type() ?: $this->wordpressContext->type();

        $relations = $hasRelatedContent
            ? $this->contentRelations->relations($siteId, $contentId, $type)
            : [];

        if ($hasRelatedContent && !$relations && $args->isStrict()) {
            return [];
        }

        $siteIds = $this->siteRelations->relatedSiteIds($siteId, $args->shouldIncludeBase());
        if (!$siteIds) {
            return [];
        }

        $languages = $this->languages->allAssignedLanguages();
        $siteIds = array_intersect($siteIds, array_keys($languages));
        if (!$siteIds) {
            return [];
        }

        return $this->buildTranslationsForSiteIds(
            $siteIds,
            $languages,
            $relations,
            $type,
            $args
        );
    }

    /**
     * @param int[] $siteIds
     * @param Language[] $languages
     * @param int[] $relations
     * @param string $type
     * @param TranslationSearchArgs $args
     * @return Translation[]
     */
    private function buildTranslationsForSiteIds(
        array $siteIds,
        array $languages,
        array $relations,
        string $type,
        TranslationSearchArgs $args
    ): array {

        $translations = [];

        foreach ($siteIds as $siteId) {
            $siteId = (int)$siteId;
            $language = $languages[$siteId] ?? null;
            if (!$language instanceof Language) {
                continue;
            }

            $translation = $this->buildTranslationDataForSiteId(
                $language,
                $siteId,
                $relations,
                $type,
                $args
            );

            if (!$translation || !$language instanceof Language) {
                continue;
            }

            $translations[$siteId] = $translation;
        }

        return $translations;
    }

    /**
     * @param Language $language
     * @param int $remoteSiteId
     * @param int[] $contentRelations
     * @param string $type
     * @param TranslationSearchArgs $args
     * @return Translation
     */
    private function buildTranslationDataForSiteId(
        Language $language,
        int $remoteSiteId,
        array $contentRelations,
        string $type,
        TranslationSearchArgs $args
    ): Translation {

        static $contentTypes;
        $contentTypes or $contentTypes = [
            WordpressContext::TYPE_SINGULAR,
            WordpressContext::TYPE_TERM_ARCHIVE,
        ];

        $translation = (new Translation($language))
            ->withType($type)
            ->withRemoteSiteId($remoteSiteId)
            ->withSourceSiteId($args->siteId());

        if (empty($contentRelations[$remoteSiteId])) {
            return $this->translationForNotRelatedContent(
                $translation,
                $remoteSiteId,
                $args,
                $type
            );
        }

        if (in_array($type, $contentTypes, true)) {
            $contentId = (int)$contentRelations[$remoteSiteId];

            return $this->translationForRelatedContent(
                $translation->withRemoteContentId($contentId),
                $remoteSiteId,
                $args->forContentId($contentId),
                $type
            );
        }

        return $translation;
    }

    /**
     * Returns the translation data for a request that is for a related content element.
     *
     * @param Translation $currentTranslation
     * @param int $remoteSiteId
     * @param TranslationSearchArgs $args
     * @param string $type
     * @return Translation
     */
    private function translationForRelatedContent(
        Translation $currentTranslation,
        int $remoteSiteId,
        TranslationSearchArgs $args,
        string $type
    ): Translation {

        $translation = null;

        switch ($type) {
            case WordpressContext::TYPE_SINGULAR:
                $translation = $this->translatorForType($type)->translationFor(
                    $remoteSiteId,
                    $args
                );
                break;
            case WordpressContext::TYPE_TERM_ARCHIVE:
                $translation = $this->translatorForType($type)->translationFor(
                    $remoteSiteId,
                    $args
                );
                break;
        }

        return $translation ? $translation->merge($currentTranslation) : $currentTranslation;
    }

    /**
     * Returns the translation data for a request that is not for a related content element.
     *
     * @param Translation $currentTranslation
     * @param int $remoteSiteId
     * @param TranslationSearchArgs $args
     * @param string $type
     * @return Translation
     */
    private function translationForNotRelatedContent(
        Translation $currentTranslation,
        int $remoteSiteId,
        TranslationSearchArgs $args,
        string $type
    ): Translation {

        $translator = $this->translatorForType($type);
        $translation = null;

        switch ($type) {
            case WordpressContext::TYPE_POST_TYPE_ARCHIVE:
            case WordpressContext::TYPE_SEARCH:
            case WordpressContext::TYPE_DATE_ARCHIVE:
                $translation = $translator->translationFor($remoteSiteId, $args);
                break;
            case WordpressContext::TYPE_HOME:
                $translation = $this
                    ->translatorForType(WordpressContext::TYPE_HOME)
                    ->translationFor($remoteSiteId, $args);
                break;
        }

        return $translation ? $translation->merge($currentTranslation) : $currentTranslation;
    }

    /**
     * @return NullTranslator
     */
    private function nullTranslator(): NullTranslator
    {
        $this->nullTranslator or $this->nullTranslator = new NullTranslator();

        return $this->nullTranslator;
    }

    /**
     * @param string $type
     * @return Translator
     */
    private function translatorForType(string $type): Translator
    {
        return $this->translators[$type] ?? $this->nullTranslator();
    }
}
