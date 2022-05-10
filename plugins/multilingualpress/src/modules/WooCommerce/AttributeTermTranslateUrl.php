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

namespace Inpsyde\MultilingualPress\Module\WooCommerce;

use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\Filter\Filter;
use Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;

/**
 * Class AttributeTermTranslateUrl
 */
class AttributeTermTranslateUrl implements Filter
{
    use FilterTrait {
        enable as private traitEnable;
    }

    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var \WP_Rewrite
     */
    private $wpRewrite;

    /**
     * AttributeTermTranslateUrlFilter constructor.
     *
     * @param \wpdb $wpdb
     * @param UrlFactory $urlFactory
     */
    public function __construct(\wpdb $wpdb, UrlFactory $urlFactory)
    {
        $this->wpdb = $wpdb;
        $this->urlFactory = $urlFactory;
    }

    /**
     * Retrieve the term link page by his term taxonomy Id.
     *
     * @param bool $checker
     * @param Translation $translation
     * @param int $siteId
     * @param TranslationSearchArgs $args
     * @return bool
     */
    public function termLinkByTaxonomyId(
        bool $checker,
        Translation $translation,
        int $siteId,
        TranslationSearchArgs $args
    ): bool {

        if (get_current_blog_id() === $siteId) {
            return false;
        }

        $termTaxonomyId = $args->contentId();

        switch_to_blog($siteId);

        if (!$this->isAttributeTaxonomy($termTaxonomyId)) {
            restore_current_blog();

            return false;
        }

        list($remoteTitle, $remoteSlug) = $this->termData($termTaxonomyId);

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $taxonomySlug = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT taxonomy from {$this->wpdb->prefix}term_taxonomy where term_taxonomy_id = %d",
                $termTaxonomyId
            )
        );
        //phpcs:enable

        $remoteUrl = $this->buildRemoteUrl($taxonomySlug, $remoteSlug);

        if ($remoteTitle and $remoteUrl) {
            $checker = true;

            $translation
                ->withRemoteTitle($remoteTitle)
                ->withRemoteUrl(
                    $this->urlFactory->create([$remoteUrl])
                );
        }

        restore_current_blog();

        return $checker;
    }

    /**
     * Lazy inject for \WP_Rewrite
     *
     * @param \WP_Rewrite $wp_rewrite
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
     * Check if the current taxonomy Id is a WooCommerce Attribute Taxonomy
     *
     * @param int $termTaxonomyId
     * @return bool
     */
    private function isAttributeTaxonomy(int $termTaxonomyId): bool
    {
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $taxonomySlug = (string)$this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT taxonomy FROM {$this->wpdb->prefix}term_taxonomy WHERE term_taxonomy_id = %d",
                $termTaxonomyId
            )
        );
        //phpcs:enable

        return false !== strpos($taxonomySlug, 'pa_');
    }

    /**
     * Build the term link for the translated term
     *
     * @param string $taxonomySlug
     * @param string $termSlug
     * @return string
     */
    private function buildRemoteUrl(string $taxonomySlug, string $termSlug): string
    {
        $termLink = $this->wpRewrite->get_extra_permastruct($taxonomySlug);

        if (!$termLink && get_option('permalink_structure', false)) {
            $termLink = $this->permalinkStructure($taxonomySlug);
        }

        if (!$termLink) {
            return $this->plainTermLink($taxonomySlug, $termSlug);
        }

        $termLink = str_replace("%$taxonomySlug%", $termSlug, $termLink);
        $termLink = home_url(user_trailingslashit($termLink, 'attribute'));

        return $termLink;
    }

    /**
     * Retrieve the term data based on term taxonomy Id
     *
     * @param int $termTaxonomyId
     * @return array
     */
    private function termData(int $termTaxonomyId): array
    {
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $termData = $this->wpdb->get_results(
            $this->wpdb->prepare(
                $this->remoteTermSql(),
                $termTaxonomyId
            ),
            ARRAY_N
        );
        //phpcs:enable

        $termData = array_filter((array)$termData);
        if (!$termData) {
            return [];
        }

        return $termData[0];
    }

    /**
     * Query string to retrieve term data
     *
     * @return string
     */
    private function remoteTermSql(): string
    {
        $sql = <<<SQL
SELECT t.name, t.slug
FROM {$this->wpdb->prefix}terms t
INNER JOIN {$this->wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
WHERE 1 = 1 AND tt.term_taxonomy_id = %d
LIMIT 1
SQL;

        return $sql;
    }

    /**
     * Build the permalink structure
     *
     * @param string $taxonomySlug
     * @return string
     */
    private function permalinkStructure(string $taxonomySlug): string
    {
        $permalinks = wc_get_permalink_structure();
        $attributeName = $this->attributeNameByTaxonomySlug($taxonomySlug);
        $rewriteSlug = trailingslashit($permalinks['attribute_rewrite_slug']);

        return "{$rewriteSlug}{$attributeName}/%{$taxonomySlug}%";
    }

    /**
     * Build the plain term url
     *
     * @param string $taxonomySlug
     * @param string $termSlug
     * @return string
     */
    private function plainTermLink(string $taxonomySlug, string $termSlug): string
    {
        return home_url("?$taxonomySlug=$termSlug");
    }

    /**
     * Retrieve the attribute name by his taxonomy
     *
     * @param string $taxonomySlug
     * @return string
     */
    private function attributeNameByTaxonomySlug(string $taxonomySlug): string
    {
        if (false === strpos($taxonomySlug, 'pa_')) {
            return $taxonomySlug;
        }

        return substr($taxonomySlug, 3);
    }
}
