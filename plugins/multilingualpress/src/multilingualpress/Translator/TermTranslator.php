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

use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use Inpsyde\MultilingualPress\Framework\Translator\Translator;

/**
 * Translator implementation for terms.
 */
class TermTranslator implements Translator
{
    use UrlBlogFragmentTrailingTrait;
    use SwitchSiteTrait;

    const FILTER_TAXONOMY_LIST = 'multilingualpress.term_translator_taxonomy_list';
    const FILTER_TRANSLATION = 'multilingualpress.filter_term_translation';
    const FILTER_TERM_PUBLIC_URL = 'multilingualpress.filter_term_public_url';

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var \WP_Rewrite
     */
    private $wpRewrite;

    /**
     * @var TaxonomyRepository
     */
    private $taxonomyRepository;

    /**
     * @var array
     */
    private $customBase = [];

    /**
     * @var \WP
     */
    private $wp;

    /**
     * @param UrlFactory $urlFactory
     */
    public function __construct(TaxonomyRepository $taxonomyRepository, UrlFactory $urlFactory)
    {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->urlFactory = $urlFactory;
    }

    /**
     * @inheritdoc
     */
    public function translationFor(int $remoteSiteId, TranslationSearchArgs $args): Translation
    {
        $translation = new Translation();

        if (!$args->contentId() || !$this->ensureWpRewrite() || !$this->ensureWp()) {
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
            $remoteSiteId,
            $args
        );

        if ($filteredTranslation) {
            return $translation;
        }

        $sourceSiteId = get_current_blog_id();

        $previousSite = $this->maybeSwitchSite($remoteSiteId);
        list($remoteTitle, $remoteUrl) = $this->translationData(
            $args->contentId(),
            $sourceSiteId,
            $remoteSiteId
        );
        $this->maybeRestoreSite($previousSite);

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
     * @param \WP|null $wp
     * @return bool
     */
    public function ensureWp(\WP $wp = null): bool
    {
        if ($this->wp && !$wp) {
            return true;
        }

        if (!$wp && empty($GLOBALS['wp'])) {
            return false;
        }

        $this->wp = $wp ?: $GLOBALS['wp'];

        return true;
    }

    /**
     * Returns the translation data for the given term taxonomy ID.
     *
     * @param int $termTaxonomyId
     * @param int $sourceSiteId
     * @param int $remoteSiteId
     * @return array
     */
    protected function translationData(
        int $termTaxonomyId,
        int $sourceSiteId,
        int $remoteSiteId
    ): array {

        $term = $this->termByTermTaxonomyId($termTaxonomyId);
        if (!$term) {
            return ['', null];
        }

        $isAdmin = is_admin();

        if ($isAdmin && current_user_can('edit_terms', $term['taxonomy'])) {
            $editUrl = get_edit_term_link((int)$term['term_id'], (string)$term['taxonomy']);

            return [
                $term['name'],
                $this->urlFactory->create([$editUrl]),
            ];
        }

        if ($isAdmin) {
            return [$term['name'], null];
        }

        $publicUrl = $this->publicUrl(
            (int)$term['term_id'],
            (string)$term['taxonomy'],
            $sourceSiteId,
            $remoteSiteId
        );

        return [
            $term['name'],
            $this->urlFactory->create([$publicUrl]),
        ];
    }

    /**
     * Returns term data according to the given term taxonomy ID.
     *
     * @param int $termTaxonomyId
     * @return array
     */
    protected function termByTermTaxonomyId(int $termTaxonomyId): array
    {
        $term = (array)get_term_by('term_taxonomy_id', $termTaxonomyId, '', ARRAY_A);
        $term = array_filter($term);

        return $term;
    }

    /**
     * Returns permalink for the given taxonomy term.
     *
     * @param int $termId
     * @param string $taxonomySlug
     * @param int $sourceSiteId
     * @param int $remoteSiteId
     * @return string
     */
    protected function publicUrl(
        int $termId,
        string $taxonomySlug,
        int $sourceSiteId,
        int $remoteSiteId
    ): string {

        $this->fixTermBase($taxonomySlug);

        $url = get_term_link($termId, $taxonomySlug);
        if (is_wp_error($url)) {
            $url = '';
        }

        return (string)apply_filters(
            self::FILTER_TERM_PUBLIC_URL,
            $url,
            $sourceSiteId,
            $remoteSiteId
        );
    }

    /**
     * Updates the global WordPress rewrite instance if it is wrong.
     *
     * @param string $taxonomySlug
     * @return void
     */
    protected function fixTermBase(string $taxonomySlug)
    {
        $struct = (string)get_option('permalink_structure', '');
        $expected = $this->expectedBase($taxonomySlug);

        if (!$struct) {
            $expected = '';
        }

        if ($struct && !$expected) {
            $expected = get_taxonomy($taxonomySlug)->rewrite['slug'];
        }

        $this->ensurePermastruct($struct);
        $this->updateRewritePermastruct($taxonomySlug, $expected);
    }

    /**
     * Finds a custom taxonomy base.
     *
     * @param string $taxonomySlug
     * @return string
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    protected function expectedBase(string $taxonomySlug): string
    {
        // phpcs:enable

        if (!$this->taxonomyRepository->isTaxonomyActive($taxonomySlug)) {
            return '';
        }

        if (in_array($taxonomySlug, array_keys($this->customBase), true)) {
            $translated = $this->customBase[$taxonomySlug]($taxonomySlug);
            return $this->composeBase(sanitize_text_field($translated), $taxonomySlug);
        }

        $option = (string)get_option($this->taxonomy($taxonomySlug), '');
        if (!$option) {
            $taxonomy = get_taxonomy($taxonomySlug);
            $slug = $taxonomy->rewrite['slug'] ?? $taxonomySlug;

            switch ($taxonomySlug) {
                case 'post_tag':
                    $option = 'tag';
                    break;
                default:
                    $option = $slug;
                    break;
            }
        }

        $remotePermalinkStructure = (string)get_option('permalink_structure');
        $hasBlogPrefix = !$taxonomy->rewrite['slug'] &&
            strpos($remotePermalinkStructure, '/blog/') !== false;

        $base = $this->composeBase($option, $taxonomySlug);

        return $this->ensureRequestFragment($base, $hasBlogPrefix);
    }

    /**
     * @param string $fragment
     * @param bool $hasBlogPrefix
     * @return string
     */
    protected function ensureRequestFragment(string $fragment, bool $hasBlogPrefix): string
    {
        if ($hasBlogPrefix) {
            $fragment = $this->trailingBlogIt($fragment);
        }

        $fragment = '/' . ltrim($fragment, '/');

        return $fragment;
    }

    /**
     * @param string $translated
     * @param string $taxonomySlug
     * @return string
     */
    protected function composeBase(string $translated, string $taxonomySlug): string
    {
        return untrailingslashit($translated) . '/%' . $taxonomySlug . '%';
    }

    /**
     * Updates the global WordPress rewrite instance for the given custom taxonomy.
     *
     * @param string $taxonomy
     * @param string $struct
     */
    protected function updateRewritePermastruct(string $taxonomy, string $struct)
    {
        $this->wpRewrite->extra_permastructs[$taxonomy]['struct'] = $struct;
    }

    /**
     * @param string $struct
     */
    protected function ensurePermastruct(string $struct)
    {
        $this->wpRewrite->permalink_structure = $struct;
    }

    /**
     * @param string $taxonomySlug
     * @return string
     */
    protected function taxonomy(string $taxonomySlug): string
    {
        $taxonomies = [
            'category' => 'category_base',
            'post_tag' => 'tag_base',
        ];

        $taxonomy = $taxonomies[$taxonomySlug] ?? '';

        return $taxonomy;
    }
}
