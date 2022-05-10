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
use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Translator\Translator;

/**
 * Translator implementation for posts.
 */
final class PostTranslator implements Translator
{
    const ACTION_GENERATE_PERMALINK = 'multilingualpress.generate_permalink';
    const ACTION_GENERATED_PERMALINK = 'multilingualpress.generated_permalink';
    const FILTER_TRANSLATION = 'multilingualpress.filter_post_translation';

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var \WP_Rewrite
     */
    private $wpRewrite;

    /**
     * @var PostTypeRepository
     */
    private $postTypeRepository;

    /**
     * @var PostTypeSlugsSettingsRepository
     */
    private $slugsRepository;

    /**
     * @var array
     */
    private $customBase = [];

    /**
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        PostTypeRepository $postTypeRepository,
        PostTypeSlugsSettingsRepository $slugsRepository,
        UrlFactory $urlFactory
    ) {

        $this->postTypeRepository = $postTypeRepository;
        $this->slugsRepository = $slugsRepository;
        $this->urlFactory = $urlFactory;
    }

    /**
     * @inheritdoc
     */
    public function translationFor(int $siteId, TranslationSearchArgs $args): Translation
    {
        $translation = new Translation();

        if (!$args->contentId()) {
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

        switch_to_blog($siteId);

        list($remoteTitle, $remoteUrl) = $this->translationData(
            $args->contentId() ?: 0,
            $args->postStatus(),
            $args->isStrict()
        );

        restore_current_blog();

        $remoteTitle and $translation = $translation->withRemoteTitle($remoteTitle);
        $remoteUrl and $translation = $translation->withRemoteUrl($remoteUrl);

        return $translation;
    }

    /**
     * @param \WP_Rewrite|null $wp_rewrite
     * @return bool
     */
    public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null): bool
    {
        if ($this->wpRewrite && !$wp_rewrite) {
            return true;
        }

        if (!$wp_rewrite && empty($GLOBALS['wp_rewrite'])) {
            return false;
        }

        $this->wpRewrite = $wp_rewrite ?: $GLOBALS['wp_rewrite'];

        return true;
    }

    /**
     * @param string $key
     * @param callable $function
     */
    public function registerBaseStructureCallback(string $key, callable $function)
    {
        $this->customBase[$key] = $function;
    }

    /**
     * Returns the translation data for the given post ID.
     *
     * @param int $postId
     * @param string[] $postStatuses
     * @param bool $strict
     * @return array
     */
    private function translationData(int $postId, array $postStatuses, bool $strict): array
    {
        $post = $postId ? get_post($postId) : null;
        if (!$post) {
            return ['', null];
        }

        if ($postStatuses && !in_array($post->post_status, $postStatuses, true)) {
            return ['', null];
        }

        $currentUserCanEdit = current_user_can('edit_post', $postId);

        if (is_admin()) {
            return $this->translationAdminData($postId, $currentUserCanEdit);
        }

        if ('publish' === $post->post_status || $currentUserCanEdit) {
            /**
             * Fires right before MultilingualPress generates a permalink.
             *
             * @param int $object_id
             */
            do_action(self::ACTION_GENERATE_PERMALINK, $postId);

            $url = $this->publicUrl($postId);

            /**
             * Fires right after MultilingualPress generated a permalink.
             *
             * @param int $object_id
             */
            do_action(self::ACTION_GENERATED_PERMALINK, $postId);

            return [
                get_the_title($postId),
                $this->urlFactory->create([$url ?: '']),
            ];
        }

        return $strict ? ['', null] : [get_the_title($postId), null];
    }

    /**
     * @param int $postId
     * @param bool $currentUserCanEdit
     * @return array
     */
    private function translationAdminData(int $postId, bool $currentUserCanEdit): array
    {
        if (!$currentUserCanEdit) {
            return ['', null];
        }

        return [
            get_the_title($postId),
            $this->urlFactory->create([get_edit_post_link($postId)]),
        ];
    }

    /**
     * @param int $postId
     * @return string
     */
    private function publicUrl(int $postId): string
    {
        $this->fixPostBase($postId);

        $url = get_permalink($postId);
        if (is_wp_error($url)) {
            $url = '';
        }

        return (string)$url;
    }

    /**
     * @param int $postId
     */
    private function fixPostBase(int $postId)
    {
        $struct = (string)get_option('permalink_structure', '');
        $postType = get_post_type($postId);
        $expected = $this->expectedBase($postType);
        $rewrite = get_post_type_object($postType)->rewrite;

        if (!$struct) {
            $expected = '';
        }

        if ($struct && !$expected && !empty($rewrite)) {
            $expected = (string)$rewrite['slug'];
        }

        $this->ensurePermastruct($struct);
        $this->updateExtraRewritePermastruct($postType, $expected);
    }

    /**
     * @param string $postType
     * @return string
     */
    private function expectedBase(string $postType): string
    {
        if (!$this->postTypeRepository->isPostTypeActive($postType)) {
            return '';
        }

        if (in_array($postType, array_keys($this->customBase), true)) {
            $translated = sanitize_text_field($this->customBase[$postType]());
            return $this->composeBase($translated, $postType);
        }

        $slugs = $this->slugsRepository->postTypeSlugs(get_current_blog_id());
        if (!isset($slugs[$postType])) {
            return '';
        }

        return $this->composeBase($slugs[$postType], $postType);
    }

    /**
     * @param string $postType
     * @param string $struct
     */
    private function updateExtraRewritePermastruct(string $postType, string $struct)
    {
        $this->wpRewrite->extra_permastructs[$postType]['struct'] = $struct;
    }

    /**
     * @param string $struct
     */
    private function ensurePermastruct(string $struct)
    {
        $this->wpRewrite->permalink_structure = $struct;
    }

    /**
     * @param string $translated
     * @param string $postType
     * @return string
     */
    private function composeBase(string $translated, string $postType): string
    {
        return untrailingslashit($translated) . '/%' . $postType . '%';
    }
}
