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

namespace Inpsyde\MultilingualPress\TranslationUi\Post;

use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\View;
use Inpsyde\MultilingualPress\Framework\Entity;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\PostMetabox;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\Field\ChangedFields;

use function Inpsyde\MultilingualPress\siteNameWithLanguage;

final class Metabox implements PostMetabox
{
    const RELATIONSHIP_TYPE = 'post';
    const ID_PREFIX = 'multilingualpress_post_translation_metabox_';
    const HOOK_PREFIX = 'multilingualpress.post_translation_metabox_';

    /**
     * @var int
     */
    private $sourceSiteId;

    /**
     * @var int
     */
    private $remoteSiteId;

    /**
     * @var ActivePostTypes
     */
    private $postTypes;

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
     * @param ActivePostTypes $postTypes
     * @param ContentRelations $contentRelations
     * @param RelationshipPermission $relationshipPermission
     */
    public function __construct(
        int $sourceSiteSite,
        int $remoteSiteId,
        ActivePostTypes $postTypes,
        ContentRelations $contentRelations,
        RelationshipPermission $relationshipPermission
    ) {

        $this->sourceSiteId = $sourceSiteSite;
        $this->remoteSiteId = $remoteSiteId;
        $this->postTypes = $postTypes;
        $this->contentRelations = $contentRelations;
        $this->fieldsHelper = new MetaboxFieldsHelper($remoteSiteId);
        $this->relationshipPermission = $relationshipPermission;
    }

    /**
     * Returns the site ID for the meta box
     *
     * @return int
     */
    public function siteId(): int
    {
        return $this->remoteSiteId;
    }

    /**
     * Know if the given post is a valid one to be in the metabox
     *
     * @param \WP_Post $post
     * @param string $saveOrShow
     * @return bool
     */
    public function acceptPost(\WP_Post $post, string $saveOrShow): bool
    {
        $postType = $post->post_type ? get_post_type_object($post->post_type) : null;
        if (!$postType instanceof \WP_Post_Type) {
            return false;
        }

        return current_user_can($postType->cap->edit_post, $post->ID)
            && $this->postTypes->arePostTypesActive($postType->name)
            && $this->relationshipPermission->isRelatedPostEditable($post, $this->siteId());
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
    public function viewForPost(\WP_Post $post): View
    {
        return new MetaboxView(
            new MetaboxFields(),
            $this->fieldsHelper,
            $this->relationshipContext($post),
            new ChangedFields()
        );
    }

    /**
     * @inheritdoc
     */
    public function actionForPost(\WP_Post $post): Action
    {
        return new MetaboxAction(
            new MetaboxFields(),
            $this->fieldsHelper,
            $this->relationshipContext($post),
            $this->postTypes,
            $this->contentRelations
        );
    }

    /**
     * Returns the meta box title for the site with the given ID
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
     * Retrieve the context for the relationship
     *
     * @param \WP_Post $sourcePost
     * @return RelationshipContext
     */
    private function relationshipContext(\WP_Post $sourcePost): RelationshipContext
    {
        if ($this->relationshipContext) {
            return $this->relationshipContext;
        }

        $this->relationshipContext = new RelationshipContext(
            [
                RelationshipContext::REMOTE_POST_ID => $this->contentRelations->contentIdForSite(
                    $this->sourceSiteId,
                    (int)$sourcePost->ID,
                    ContentRelations::CONTENT_TYPE_POST,
                    $this->siteId()
                ),
                RelationshipContext::REMOTE_SITE_ID => $this->siteId(),
                RelationshipContext::SOURCE_POST_ID => (int)$sourcePost->ID,
                RelationshipContext::SOURCE_SITE_ID => $this->sourceSiteId,
            ]
        );

        return $this->relationshipContext;
    }
}
