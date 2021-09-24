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

use Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Translator\Translator;

/**
 * Translator implementation for post types.
 */
final class PostTypeTranslator implements Translator
{
    const FILTER_POST_TYPE_PERMALINK = 'multilingualpress.post_type_permalink';
    const FILTER_TRANSLATION = 'multilingualpress.filter_post_type_translation';

    /**
     * @var ActivePostTypes
     */
    private $activePostTypes;

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var PostTypeSlugsSettingsRepository
     */
    private $slugsRepository;

    /**
     * @param UrlFactory $urlFactory
     * @param ActivePostTypes $activePostTypes
     */
    public function __construct(
        PostTypeSlugsSettingsRepository $slugsRepository,
        UrlFactory $urlFactory,
        ActivePostTypes $activePostTypes
    ) {

        $this->urlFactory = $urlFactory;
        $this->slugsRepository = $slugsRepository;
        $this->activePostTypes = $activePostTypes;
    }

    /**
     * @inheritdoc
     */
    public function translationFor(int $siteId, TranslationSearchArgs $args): Translation
    {
        $translation = new Translation();

        $postType = $args->postType();

        if (!$this->activePostTypes->arePostTypesActive($postType)) {
            return $translation;
        }

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

        $postTypeObject = get_post_type_object($postType);
        $previousPostTypeSlug = $postTypeObject->rewrite['slug'];

        switch_to_blog($siteId);

        $postTypeSlug = $this->slugsRepository->postTypeSlugs($siteId)[$postType] ?? '';
        $postTypeSlug and $postTypeObject->rewrite['slug'] = $postTypeSlug;

        $postTypePermalink = (string)get_post_type_archive_link($postType);

        /**
         * Filter Post Type Permalink for Post Type Translation archive.
         *
         * @param string $postTypePermalink The permalink of the post type archive.
         */
        $postTypePermalink = apply_filters(self::FILTER_POST_TYPE_PERMALINK, $postTypePermalink);

        $url = $this->urlFactory->create([$postTypePermalink]);
        $translation = $translation->withRemoteUrl($url);

        if ($postTypeObject) {
            $translation = $translation->withRemoteTitle($postTypeObject->labels->name);
        }

        restore_current_blog();

        $postTypeObject->rewrite['slug'] = $previousPostTypeSlug;

        return $translation;
    }
}
