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

namespace Inpsyde\MultilingualPress\TranslationUi\Term;

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;

use function Inpsyde\MultilingualPress\siteExists;

/**
 * Permission checker to be used to either permit or prevent access to terms.
 */
class RelationshipPermission
{
    const FILTER_IS_RELATED_TERM_EDITABLE = 'multilingualpress.is_related_term_editable';

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var int[][]
     */
    private $relatedTerms = [];

    /**
     * @param ContentRelations $contentRelations
     */
    public function __construct(ContentRelations $contentRelations)
    {
        $this->contentRelations = $contentRelations;
    }

    /**
     * Checks if the current user can edit (or create) a term in the site with the given ID that is
     * related to given term in the current site.
     *
     * @param \WP_Term $sourceTerm
     * @param int $remoteSiteId
     * @return bool
     */
    public function isRelatedTermEditable(\WP_Term $sourceTerm, int $remoteSiteId): bool
    {
        if (!siteExists($remoteSiteId)) {
            return false;
        }

        switch_to_blog($remoteSiteId);

        $taxonomy = get_taxonomy($sourceTerm->taxonomy);
        if (!$taxonomy instanceof \WP_Taxonomy) {
            return false;
        }

        $isTermEditable = current_user_can($taxonomy->cap->edit_terms);

        $relatedTerm = null;
        $relatedTermTaxonomyId = $this->relatedTermTaxonomyId(
            (int)$sourceTerm->term_taxonomy_id,
            $remoteSiteId
        );

        if ($relatedTermTaxonomyId) {
            // This is just to be extra careful in case the term has been deleted via MySQL etc.
            $relatedTerm = get_term_by('term_taxonomy_id', $relatedTermTaxonomyId);
            $isTermEditable =
                $relatedTerm instanceof \WP_Term
                && current_user_can('edit_term', $relatedTerm->term_id);
        }
        restore_current_blog();

        /**
         * Filters if the related term of the given term in the given site is editable.
         *
         * @param bool $isTermEditable
         * @param \WP_Term $sourceTerm
         * @param int $remoteSiteId
         * @param \WP_Term|null $relatedTerm
         * @param \WP_Taxonomy $taxonomy
         */
        return (bool)apply_filters(
            self::FILTER_IS_RELATED_TERM_EDITABLE,
            $isTermEditable,
            $sourceTerm,
            $remoteSiteId,
            $relatedTerm,
            $taxonomy
        );
    }

    /**
     * Returns an array with the IDs of all related terms for the term with the given ID as an
     * array with site IDs as keys and term IDs as values.
     *
     * @param int $termTaxonomyId
     * @param int $remoteSiteId
     * @return int
     */
    private function relatedTermTaxonomyId(int $termTaxonomyId, int $remoteSiteId): int
    {
        if (!array_key_exists($termTaxonomyId, $this->relatedTerms)) {
            $this->relatedTerms[$termTaxonomyId] = $this->contentRelations->relations(
                get_current_blog_id(),
                $termTaxonomyId,
                ContentRelations::CONTENT_TYPE_TERM
            );
        }

        return (int)($this->relatedTerms[$termTaxonomyId][$remoteSiteId] ?? 0);
    }
}
