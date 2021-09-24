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

/**
 * Multilingualpress API helpers.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\Languages;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Language\Language;
use Inpsyde\MultilingualPress\Framework\Language\NullLanguage;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Language\EmbeddedLanguage;
use Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar\SettingsRepository;
use Inpsyde\MultilingualPress\Module\LanguageManager;

/**
 * Returns the content IDs of all translations for the given content element data.
 *
 * @param int $contentId
 * @param string $type
 * @param int $siteId
 * @return int[]
 * @throws NonexistentTable
 */
function translationIds(int $contentId = 0, string $type = 'post', int $siteId = 0): array
{
    $contentId = defaultContentId($contentId);
    if (!$contentId) {
        return [];
    }

    $api = resolve(ContentRelations::class);

    $siteId or $siteId = get_current_blog_id();

    return $api->relations($siteId, $contentId, $type);
}

/**
 * Returns the MultilingualPress language for the site with the given ID.
 *
 * @param int $siteId
 * @return string
 * @throws NonexistentTable
 */
function siteLocale(int $siteId = 0): string
{
    $tag = resolve(SiteSettingsRepository::class)->siteLanguageTag($siteId);
    if (!$tag) {
        return '';
    }

    return languageByTag($tag)->locale();
}

/**
 * Returns the MultilingualPress language for the site with the given ID.
 *
 * @param int $siteId
 * @return string
 */
function siteLanguageTag(int $siteId = 0): string
{
    return resolve(SiteSettingsRepository::class)->siteLanguageTag($siteId);
}

/**
 * Returns the MultilingualPress locale name for the site with the given ID.
 *
 * @param int $siteId
 * @return string
 * @throws NonexistentTable
 */
function siteLocaleName(int $siteId = 0): string
{
    $tag = resolve(SiteSettingsRepository::class)->siteLanguageTag($siteId);
    if (!$tag) {
        return '';
    }

    return languageByTag($tag)->name();
}

/**
 * Returns the MultilingualPress language name for the site with the given ID.
 *
 * @param int $siteId
 * @return string
 * @throws NonexistentTable
 */
function siteLanguageName(int $siteId = 0): string
{
    $name = siteLocaleName($siteId);
    if (substr_count($name, ' (')) {
        $name = strtok($name, ' (');
    }

    return $name;
}

/**
 * Returns the MultilingualPress site name (which includes site name and site language) for the site
 * with the given ID.
 *
 * @param int $siteId
 * @return string
 * @throws NonexistentTable
 */
function siteNameWithLanguage(int $siteId = 0): string
{
    $localeName = siteLocaleName($siteId) ?: siteLocale($siteId);
    $languageName = $localeName;

    $siteName = resolve(SettingsRepository::class)->alternativeLanguageTitle($siteId);
    $siteName or $siteName = (string)get_blog_option($siteId, 'blogname', '');

    if (!$siteName) {
        // translators: %s is the language name, e.g. "English (US)"
        $format = _x('%s Site', 'sites relationship', 'multilingualpress');
        $siteName = sprintf($format, $languageName);
        $languageName = '';
    }

    if (!$siteName) {
        $localeName and $languageName = $localeName;
        // translators: %d is the site ID
        $format = _x('Site %d', 'sites relationship', 'multilingualpress');
        $siteName = sprintf($format, $siteId);
    }

    $languageName and $siteName .= " - {$languageName}";

    return (string)apply_filters(
        'multilingualpress.site_name_with_language',
        $siteName,
        $siteId,
        $localeName
    );
}

/**
 * Return all available languages, including default and DB.
 * Array keys are BCP-47 tags.
 *
 * @return Language[]
 * @throws NonexistentTable
 */
function allLanguages(): array
{
    $languages = allDefaultLanguages();
    $moduleManager = resolve(ModuleManager::class);
    if ($moduleManager->isModuleActive(LanguageManager\ServiceProvider::MODULE_ID)) {
        $dbLanguages = resolve(Languages::class)->allLanguages();
        foreach ($dbLanguages as $dbLanguage) {
            $tag = $dbLanguage->bcp47tag();
            $tag and $languages[$tag] = $dbLanguage;
        }
    }

    return $languages;
}

/**
 * Returns the names of all available languages according to the given arguments.
 *
 * @param bool $onlyRelatedToCurrentSite
 * @param bool $includeCurrentSite
 * @return string[]
 * @throws NonexistentTable
 */
function assignedLanguageNames(
    bool $onlyRelatedToCurrentSite = true,
    bool $includeCurrentSite = true
): array {

    $currentSiteId = get_current_blog_id();
    /** @var null|array $relatedSites */
    $relatedSites = null;

    if ($onlyRelatedToCurrentSite) {
        $relations = resolve(SiteRelations::class);
        $relatedSites = $relations->relatedSiteIds($currentSiteId, $includeCurrentSite);
        if (!$relatedSites) {
            return [];
        }
    }

    $languages = assignedLanguages();
    $names = [];
    foreach ($languages as $siteId => $language) {
        /** @var int $siteId */
        if (!$includeCurrentSite && $siteId === $currentSiteId) {
            continue;
        }
        if ($relatedSites === null || in_array($siteId, $relatedSites, true)) {
            $name = $language->name();
            $name and $names[$siteId] = $name;
        }
    }

    return $names;
}

/**
 * Returns the individual MultilingualPress language code of all (related) sites.
 *
 * @param bool $relatedSitesOnly
 * @param bool $includeCurrentSite
 * @return string[]
 * @throws NonexistentTable
 */
function assignedLanguageTags(
    bool $relatedSitesOnly = true,
    bool $includeCurrentSite = true
): array {

    /** @var null|array $relations */
    $relations = null;
    if ($relatedSitesOnly) {
        $relations = resolve(SiteRelations::class)->allRelations();
        if (!$relations) {
            return [];
        }
    }

    $languages = assignedLanguages();
    $codes = [];
    $currentSiteId = get_current_blog_id();
    /** @var int $siteId */
    foreach ($languages as $siteId => $language) {
        if (!$includeCurrentSite && $siteId === $currentSiteId) {
            continue;
        }
        if ($relations === null || array_key_exists($siteId, $relations)) {
            $code = $language->bcp47tag();
            $code and $codes[$siteId] = $code;
        }
    }

    return $codes;
}

/**
 * Returns the individual MultilingualPress language object of all (related) sites.
 *
 * @param bool $relatedSitesOnly
 * @return Language[]
 * @throws NonexistentTable
 */
function assignedLanguages(bool $relatedSitesOnly = true): array
{
    /** @var null|array $relations */
    $relations = null;
    if ($relatedSitesOnly) {
        $relations = resolve(SiteRelations::class)->allRelations();
        if (!$relations) {
            return [];
        }
    }

    $languagesApi = resolve(Languages::class);
    $languages = $languagesApi->allAssignedLanguages();
    $relations and $languages = array_intersect_key($languages, $relations);

    return $languages;
}

/**
 * Returns the MultilingualPress language for the current site.
 *
 * @return string
 * @throws NonexistentTable
 */
function currentSiteLocale(): string
{
    return siteLocale(get_current_blog_id());
}

/**
 * Returns the language with the given BCP-47 tag.
 *
 * @param string $bcp47tag
 * @return Language
 * @throws NonexistentTable
 */
function languageByTag(string $bcp47tag): Language
{
    $api = resolve(Languages::class);
    $language = new NullLanguage();
    $moduleManager = resolve(ModuleManager::class);

    if ($moduleManager->isModuleActive(LanguageManager\ServiceProvider::MODULE_ID)) {
        $language = $api->languageBy(LanguagesTable::COLUMN_BCP_47_TAG, $bcp47tag);
    }

    if ($language instanceof NullLanguage) {
        $language = defaultLanguageByTag($bcp47tag);
    }

    return $language;
}

/**
 * @return Language[]
 */
function allDefaultLanguages(): array
{
    static $languages;
    if (is_array($languages)) {
        return $languages;
    }

    $jsonPath = resolve(PluginProperties::class)->dirPath() . 'public/json/languages-wp.json';
    $encoded = @file_get_contents($jsonPath);
    if (!$encoded) {
        $languages = [];

        return [];
    }

    $json = @json_decode($encoded, true);
    $languages = [];

    if (!$json || !is_array($json)) {
        return [];
    }

    $main = [];
    /** @var array[] $json */
    foreach ($json as $languageData) {
        $bcp47 = $languageData['bcp47'] ?? '';
        if (!$bcp47 || !is_string($bcp47) || !is_array($languageData)) {
            continue;
        }
        $language = EmbeddedLanguage::fromJsonData($languageData);
        if ($language->type() !== EmbeddedLanguage::TYPE_LANGUAGE) {
            $main[$language->parentLanguageTag()] = 1;
        }
        $languages[$bcp47] = $language;
    }

    $main and $languages = array_diff_key($languages, $main);

    return $languages;
}

/**
 * @param string $bcp47tag
 * @return Language
 */
function defaultLanguageByTag(string $bcp47tag): Language
{
    static $cache = [];
    if (array_key_exists($bcp47tag, $cache)) {
        return $cache[$bcp47tag];
    }

    $all = allDefaultLanguages();
    if (array_key_exists($bcp47tag, $all)) {
        $cache[$bcp47tag] = $all[$bcp47tag];

        return $cache[$bcp47tag];
    }

    if (substr_count($bcp47tag, '-')) {
        $cache[$bcp47tag] = new NullLanguage();

        return $cache[$bcp47tag];
    }

    if (strlen($bcp47tag) === 2) {
        $try = "{$bcp47tag}-" . strtoupper($bcp47tag);
        if (array_key_exists($try, $all)) {
            $cache[$bcp47tag] = $all[$try];

            return $cache[$bcp47tag];
        }
    }

    foreach ($all as $language) {
        if (stripos($language->bcp47tag(), $bcp47tag) === 0) {
            $cache[$bcp47tag] = $language;

            return $cache[$bcp47tag];
        }
    }

    $cache[$bcp47tag] = new NullLanguage();

    return $cache[$bcp47tag];
}
