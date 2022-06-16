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

use Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\View;
use Inpsyde\MultilingualPress\Framework\Entity;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\TermMetabox;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

use function Inpsyde\MultilingualPress\siteNameWithLanguage;

final class Metabox implements TermMetabox
{
    const RELATIONSHIP_TYPE = 'term';
    const ID_PREFIX = 'multilingualpress_term_translation_metabox_';
    const HOOK_PREFIX = 'multilingualpress_.term_translation_metabox_';

    /**
     * @var int
     */
    private $sourceSiteId;

    /**
     * @var int
     */
    private $remoteSiteId;

    /**
     * @var ActiveTaxonomies
     */
    private $taxonomies;

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var RelationshipPermission
     */
    private $relationshipPermission;

    /**
     * @var MetaboxFieldsHelper
     */
    private $fieldsHelper;

    /**
     * @var RelationshipContext
     */
    private $relationshipContext;

    /**
     * @param int $sourceSiteSite
     * @param int $remoteSiteId
     * @param ActiveTaxonomies $taxonomies
     * @param ContentRelations $contentRelations
     * @param RelationshipPermission $relationshipPermission
     */
    public function __construct(
        int $sourceSiteSite,
        int $remoteSiteId,
        ActiveTaxonomies $taxonomies,
        ContentRelations $contentRelations,
        RelationshipPermission $relationshipPermission
    ) {

        $this->sourceSiteId = $sourceSiteSite;
        $this->remoteSiteId = $remoteSiteId;
        $this->taxonomies = $taxonomies;
        $this->contentRelations = $contentRelations;
        $this->fieldsHelper = new MetaboxFieldsHelper($remoteSiteId);
        $this->relationshipPermission = $relationshipPermission;
    }

    /**
     * Returns the site ID for the meta box.
     * @return int
     */
    public function siteId(): int
    {
        return $this->remoteSiteId;
    }

    /**
     * @param \WP_Term $term
     * @param string $saveOrShow
     * @return bool
     */
    public function acceptTerm(\WP_Term $term, string $saveOrShow): bool
    {
        $taxonomy = $term->taxonomy ? get_taxonomy($term->taxonomy) : null;
        if (!$taxonomy instanceof \WP_Taxonomy) {
            return false;
        }

        return current_user_can($taxonomy->cap->edit_terms, $term->term_id)
            && $this->taxonomies->areTaxonomiesActive($taxonomy->name)
            && $this->relationshipPermission->isRelatedTermEditable($term, $this->siteId());
    }

    /**
     * @inheritdoc
     */
    public function createInfo(string $showOrSave, Entity $entity): Info
    {
        return new Info($this->buildBoxTitle(), self::ID_PREFIX . $this->siteId());
    }

    /**
     * @inheritdoc
     */
    public function viewForTerm(\WP_Term $term): View
    {
        return new MetaboxView(
            new MetaboxFields(),
            $this->fieldsHelper,
            $this->relationshipContext($term)
        );
    }

    /**
     * @inheritdoc
     */
    public function actionForTerm(\WP_Term $term): Action
    {
        return new MetaboxAction(
            new MetaboxFields(),
            $this->fieldsHelper,
            $this->relationshipContext($term),
            $this->taxonomies,
            $this->contentRelations
        );
    }

    /**
     * Returns the meta box title for the site with the given ID.
     *
     * @return string
     */
    private function buildBoxTitle(): string
    {
        /* translators: %s is site name including language */
        $titleFormat = __('Translation for "%s"', 'multilingualpress');

        $title = sprintf(
            $titleFormat,
            siteNameWithLanguage($this->siteId())
        );

        return $title;
    }

    /**
     * @param \WP_Term $sourceTerm
     * @return RelationshipContext
     */
    private function relationshipContext(\WP_Term $sourceTerm): RelationshipContext
    {
        if ($this->relationshipContext) {
            return $this->relationshipContext;
        }

        $this->relationshipContext = new RelationshipContext(
            [
                RelationshipContext::REMOTE_TERM_ID => $this->contentRelations->contentIdForSite(
                    $this->sourceSiteId,
                    (int)$sourceTerm->term_taxonomy_id,
                    ContentRelations::CONTENT_TYPE_TERM,
                    $this->siteId()
                ),
                RelationshipContext::REMOTE_SITE_ID => $this->siteId(),
                RelationshipContext::SOURCE_TERM_ID => (int)$sourceTerm->term_taxonomy_id,
                RelationshipContext::SOURCE_SITE_ID => $this->sourceSiteId,
            ]
        );

        return $this->relationshipContext;
    }
}
