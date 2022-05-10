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

namespace Inpsyde\MultilingualPress\Translator;

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Translator\Translator;
use Inpsyde\MultilingualPress\Framework\WordpressContext;

/**
 * Translator implementation for front-page requests.
 */
final class HomeTranslator implements Translator
{
    const FILTER_TRANSLATION = 'multilingualpress.filter_home_translation';

    const SHOW_ON_FRONT_POSTS = 'posts';
    const SHOW_ON_FRONT_PAGE = 'page';

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * HomeTranslator constructor.
     * @param UrlFactory $urlFactory
     * @param ContentRelations $contentRelations
     */
    public function __construct(UrlFactory $urlFactory, ContentRelations $contentRelations)
    {
        $this->urlFactory = $urlFactory;
        $this->contentRelations = $contentRelations;
    }

    /**
     * @inheritdoc
     */
    public function translationFor(int $siteId, TranslationSearchArgs $args): Translation
    {
        $url = $this->url($siteId, $args);

        $translation = new Translation();

        /**
         * Filter Translation bypassing the translation
         *
         * @param bool false True to by pass
         * @param Translation $translation
         * @param int $siteId
         * @param TranslationSearchArgs $args
         */
        $filteredTranslation = apply_filters(
            self::FILTER_TRANSLATION,
            false,
            $translation,
            $siteId,
            $args
        );

        if ($filteredTranslation) {
            return $translation;
        }

        $url = $this->urlFactory->create([$url]);

        return $translation->withRemoteUrl($url);
    }

    /**
     * Retrieve the translation url
     *
     * @param int $siteId
     * @param TranslationSearchArgs $args
     * @return string
     */
    private function url(int $siteId, TranslationSearchArgs $args): string
    {
        $url = '';
        $originalSiteId = $this->maybeSwitchSite($siteId);
        $homeUrl = get_home_url($siteId, '/');

        if (is_home() && is_front_page()) {
            $url = $homeUrl;
        }

        if (!$url) {
            $url = $this->showOnFrontUrl($originalSiteId, $siteId, $homeUrl, $args);
        }

        $this->maybeRestoreSite($originalSiteId);

        return $url;
    }

    /**
     * Retrieve the url used by the show on front option
     *
     * @param int $originalSiteId
     * @param int $siteId
     * @param string $homeUrl
     * @param TranslationSearchArgs $args
     * @return string
     */
    private function showOnFrontUrl(
        int $originalSiteId,
        int $siteId,
        string $homeUrl,
        TranslationSearchArgs $args
    ): string {

        $option = get_option('show_on_front');

        if ($option === self::SHOW_ON_FRONT_POSTS) {
            return $homeUrl;
        }

        if ($option === self::SHOW_ON_FRONT_PAGE) {
            $relationshipIds = [];
            $pageForPostsId = (int)get_option('page_for_posts');
            $frontPageId = (int)get_option('page_on_front');

            $args->contentId() and $relationshipIds = $this->contentRelations->relations(
                $originalSiteId,
                $args->contentId(),
                WordpressContext::TYPE_SINGULAR
            );

            // Treat it as translation for related content
            if (!$pageForPostsId && !empty($relationshipIds) && !$args->isStrict()) {
                return get_permalink($relationshipIds[$siteId]) ?: '';
            }

            // The same as SHOW_ON_FRONT_POSTS
            if (!$frontPageId && $pageForPostsId) {
                return $homeUrl;
            }

            $url = $pageForPostsId ? get_permalink($pageForPostsId) : '';

            // Fallback to the homepage in case not a strict search
            if (!$url && !$args->isStrict()) {
                return $homeUrl;
            }
        }

        return '';
    }

    /**
     * @param int $remoteSiteId
     * @return int
     */
    private function maybeSwitchSite(int $remoteSiteId): int
    {
        $currentSite = get_current_blog_id();
        if ($currentSite !== $remoteSiteId) {
            switch_to_blog($remoteSiteId);

            return $currentSite;
        }

        return -1;
    }

    /**
     * @param int $originalSiteId
     * @return bool
     */
    private function maybeRestoreSite(int $originalSiteId): bool
    {
        if ($originalSiteId < 0) {
            return false;
        }

        restore_current_blog();

        $currentSite = get_current_blog_id();
        if ($currentSite !== $originalSiteId) {
            switch_to_blog($originalSiteId);
        }

        return true;
    }
}
