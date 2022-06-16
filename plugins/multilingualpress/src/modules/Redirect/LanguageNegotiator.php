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

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\Translations;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Language\Language;
use Inpsyde\MultilingualPress\Framework\WordpressContext;
use Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository;

class LanguageNegotiator
{
    const FILTER_REDIRECT_URL = 'multilingualpress.redirect_url';
    const FILTER_POST_STATUS = 'multilingualpress.redirect_post_status';
    const FILTER_PRIORITY_FACTOR = 'multilingualpress.language_only_priority_factor';
    const FILTER_REDIRECT_TARGETS = 'multilingualpress.redirect_targets';

    /**
     * @var float
     */
    private $languageOnlyPriorityFactor;

    /**
     * @var AcceptLanguageParser
     */
    private $parser;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Translations
     */
    private $translations;


    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Translations $translations
     * @param Request $request
     * @param AcceptLanguageParser $parser
     * @param Repository $repository
     */
    public function __construct(
        Translations $translations,
        Request $request,
        AcceptLanguageParser $parser,
        Repository $repository
    ) {

        $this->translations = $translations;
        $this->request = $request;
        $this->parser = $parser;
        $this->repository = $repository;

        /**
         * Filters the factor used to compute the priority of language-only matches.
         * This has to be between 0 and 1.
         *
         * @param float $factor
         *
         * @see get_user_priority()
         */
        $factor = (float)apply_filters(static::FILTER_PRIORITY_FACTOR, .8);

        $this->languageOnlyPriorityFactor = (float)max(0, min(1, $factor));
    }

    /**
     * Returns the redirect target data object for the best-matching language version.
     *
     * @param TranslationSearchArgs|null $args
     * @return RedirectTarget
     */
    public function redirectTarget(TranslationSearchArgs $args = null): RedirectTarget
    {
        $targets = $this->redirectTargets($args);

        if (!$targets) {
            return new RedirectTarget();
        }

        $targets = array_filter(
            $targets,
            static function (RedirectTarget $target): bool {
                return 0 < $target->userPriority();
            }
        );

        if (!$targets) {
            return new RedirectTarget();
        }
        uasort(
            $targets,
            static function (RedirectTarget $left, RedirectTarget $right): int {
                $leftPriority = $left->priority() * $left->userPriority() * $left->languageFallbackPriority();
                $rightPriority = $right->priority() * $right->userPriority() * $right->languageFallbackPriority();

                return $rightPriority <=> $leftPriority;
            }
        );

        return reset($targets);
    }

    /**
     * Returns the redirect target data objects for all available language versions.
     *
     * @param TranslationSearchArgs|null $args
     * @return RedirectTarget[]
     */
    public function redirectTargets(TranslationSearchArgs $args = null): array
    {
        $currentSiteId = get_current_blog_id();
        $translations = $this->searchTranslations($args ?: new TranslationSearchArgs());
        $targets = [];
        $userLanguages = $this->userLanguages();

        foreach ($translations as $siteId => $translation) {
            $language = $translation->language();
            $remoteUrl = $translation->remoteUrl();

            /**
             * Filters the redirect URL.
             *
             * @param string $remoteUrl
             * @param Language $language
             * @param Translation $translation
             * @param int $currentSiteId
             */
            $url = (string)apply_filters(
                self::FILTER_REDIRECT_URL,
                $remoteUrl,
                $language,
                $translation,
                $currentSiteId
            );

            $userPriority = $this->languagePriority($language, $userLanguages);
            $languageFallbackPriority = $this->languageFallbackPriority($siteId);

            $targets[] = new RedirectTarget(
                [
                    RedirectTarget::KEY_CONTENT_ID => $translation->remoteContentId(),
                    RedirectTarget::KEY_LANGUAGE => $language->bcp47tag(),
                    RedirectTarget::KEY_PRIORITY => 1,
                    RedirectTarget::KEY_SITE_ID => $siteId,
                    RedirectTarget::KEY_URL => $url,
                    RedirectTarget::KEY_USER_PRIORITY => $userPriority,
                    RedirectTarget::KEY_LANGUAGE_FALLBACK_PRIORITY => $languageFallbackPriority,
                ]
            );
        }

        return $this->orderTargets($targets, $translations);
    }

    /**
     * @param RedirectTarget[] $targets
     * @param Translation[] $translations
     * @return RedirectTarget[]
     */
    private function orderTargets(array $targets, array $translations): array
    {
        /**
         * Filters the possible redirect target objects.
         *
         * @param RedirectTarget[] $targets
         * @param Translation[] $translations
         */
        $targets = (array)apply_filters(
            self::FILTER_REDIRECT_TARGETS,
            $targets,
            $translations
        );

        if (!$targets) {
            return [];
        }

        $targets = array_filter(
            $targets,
            function ($target): bool { // phpcs:ignore
                return $target instanceof RedirectTarget;
            }
        );

        if (!$targets) {
            return [];
        }

        uasort(
            $targets,
            static function (RedirectTarget $left, RedirectTarget $right): int {
                return $right->priority() <=> $left->priority();
            }
        );

        return $targets;
    }

    /**
     * Returns all translations according to the given arguments.
     *
     * @param TranslationSearchArgs|null $args
     * @return Translation[]
     */
    private function searchTranslations(TranslationSearchArgs $args = null): array
    {
        /**
         * Filters the allowed status for posts to be included as possible redirect targets.
         *
         * @param string[] $postStatuses
         */
        $postStatuses = (array)apply_filters(self::FILTER_POST_STATUS, ['publish']);

        $args or $args = new TranslationSearchArgs();

        $context = new WordpressContext();
        $args->forContentId($context->queriedObjectId())
            ->forSiteId(get_current_blog_id())
            ->forPostType($context->postType())
            ->searchFor(get_search_query())
            ->forType($context->type());

        $args->includeBase()->forPostStatus(...array_filter($postStatuses, 'is_string'));

        $translations = $this->translations->searchTranslations($args);

        return array_filter(
            $translations,
            static function (Translation $translation): bool {
                return (bool)$translation->remoteUrl();
            }
        );
    }

    /**
     * Returns the user languages included in the Accept-Language header as an array with language
     * codes as keys, and priorities as values.
     *
     * @return float[]
     */
    private function userLanguages(): array
    {
        $fields = $this->parser->parseHeader($this->request->header('Accept-Language'));

        if (!$fields) {
            return [];
        }

        $userLanguages = [];
        foreach ($fields as $code => $priority) {
            $userLanguages[strtolower($code)] = $priority;
            if (!strpos($code, '-')) {
                continue;
            }
            $code = strtolower(strtok($code, '-'));
            if (!isset($userLanguages[$code])) {
                $userLanguages[$code] = $priority;
            }
        }

        return $userLanguages;
    }

    /**
     * Returns the priority of the given language.
     *
     * @param Language $language
     * @param float[] $languages
     * @return float
     */
    private function languagePriority(Language $language, array $languages): float
    {
        $tag = $this->languageTag($language);

        if (isset($languages[$tag])) {
            return (float)$languages[$tag];
        }

        if (substr_count($tag, '-')) {
            $tag = strtok($tag, '-');
            if (isset($languages[$tag])) {
                return $this->languageOnlyPriorityFactor * $languages[$tag];
            }
        }

        return 0.0;
    }

    /**
     * The Method will get the language tag
     * It will also fix the language tags for language variants
     * and will remove the third part from language ta so de-DE-formal will become de-DE
     *
     * @param Language $language The language Object
     * @return string The language bcp47 tag
     */
    private function languageTag(Language $language): string
    {
        $languageTag = strtolower($language->bcp47tag());
        if ($language->type() !== 'variant') {
            return $languageTag;
        }

        $languageParts = explode('-', $languageTag);
        $languageTag = $languageParts[0] . '-' . $languageParts[1];

        return $languageTag;
    }

    /**
     * Calculate the redirect language fallback priority
     *
     * @param int $siteId
     * @return int The redirect language fallback priority
     */
    protected function languageFallbackPriority(int $siteId): int
    {
        $redirectFallback = $this->repository->isRedirectSettingEnabledForSite($siteId, $this->repository::OPTION_SITE_ENABLE_REDIRECT_FALLBACK);

        return $redirectFallback ? 2 : 1;
    }
}
