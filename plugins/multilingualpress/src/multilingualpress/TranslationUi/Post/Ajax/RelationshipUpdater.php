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

namespace Inpsyde\MultilingualPress\TranslationUi\Post\Ajax;

use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Entity;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox as FrameworkMetabox;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\Post\Metabox;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipPermission;

/**
 * Multilingualpress Relationship Ajax Updater for Posts
 */
class RelationshipUpdater
{
    const ACTION = 'multilingualpress_update_post_relationship';
    const TASK_PARAM = 'task';

    const TASK_METHOD_MAP = [
        MetaboxFields::FIELD_RELATION_EXISTING => 'connectExistingPost',
        MetaboxFields::FIELD_RELATION_REMOVE => 'disconnectPost',
        MetaboxFields::FIELD_RELATION_NEW => 'newRelationPost',
    ];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @var string
     */
    private $lastError = 'Unknown error.';

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var ActivePostTypes
     */
    private $postTypes;

    /**
     * @var RelationshipPermission
     */
    private $relationshipPermission;

    /**
     * @param Request $request
     * @param ContextBuilder $contextBuilder
     * @param ContentRelations $contentRelations
     * @param ActivePostTypes $postTypes
     * @param RelationshipPermission $relationshipPermission
     */
    public function __construct(
        Request $request,
        ContextBuilder $contextBuilder,
        ContentRelations $contentRelations,
        ActivePostTypes $postTypes,
        RelationshipPermission $relationshipPermission
    ) {

        $this->request = $request;
        $this->contextBuilder = $contextBuilder;
        $this->contentRelations = $contentRelations;
        $this->postTypes = $postTypes;
        $this->relationshipPermission = $relationshipPermission;
    }

    /**
     * Handle AJAX request.
     *
     * @see RelationshipUpdater::connectExistingPost()
     * @see RelationshipUpdater::disconnectPost()
     * @see RelationshipUpdater::newRelationPost()
     */
    public function handle()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_' . self::ACTION)) {
            wp_send_json_error('Invalid action.');
        }

        $task = (string)$this->request->bodyValue(
            self::TASK_PARAM,
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );

        $methodName = $task ? self::TASK_METHOD_MAP[$task] ?? '' : '';
        if (!$methodName) {
            wp_send_json_error('Invalid task.');
        }

        $context = $this->contextBuilder->build();
        $remoteSiteId = $context->remoteSiteId();

        $result = ([$this, $methodName])($context);
        if ($result) {
            $post = $context->sourcePost();
            $metabox = new Metabox(
                $context->sourceSiteId(),
                $remoteSiteId,
                $this->postTypes,
                $this->contentRelations,
                $this->relationshipPermission
            );

            $info = $metabox->createInfo(FrameworkMetabox::SHOW, new Entity($post));

            switch_to_blog($remoteSiteId);
            ob_start();
            $metabox->viewForPost($post)->render($info);
            $rendered = ob_get_clean();
            restore_current_blog();

            wp_send_json_success($rendered);
        }

        wp_send_json_error($this->lastError);
    }

    /**
     * Connects the current post with an existing remote one.
     *
     * @param RelationshipContext $context
     * @return bool
     */
    private function connectExistingPost(RelationshipContext $context): bool
    {
        $contentIds = [$context->sourceSiteId() => $context->sourcePostId()];

        $relationshipId = $this->contentRelations->relationshipId(
            $contentIds,
            ContentRelations::CONTENT_TYPE_POST
        );

        if (!$relationshipId) {
            $relationshipId = $this->contentRelations->relationshipId(
                [$context->remoteSiteId() => $context->remotePostId()],
                ContentRelations::CONTENT_TYPE_POST,
                true
            );
        }

        if (!$relationshipId) {
            $this->lastError = 'Error saving relation';

            return false;
        }

        $contentIds[$context->remoteSiteId()] = $context->remotePostId();

        foreach ($contentIds as $siteId => $postId) {
            if (!$this->contentRelations->saveRelation($relationshipId, $siteId, $postId)) {
                $this->lastError = "Error saving relation for site {$siteId} and post {$postId}.";

                return false;
            }
        }

        return true;
    }

    /**
     * New Relationship markup can be retrieved after the metabox is saved, in this case we simply
     * return true since the new post and the relationship already exists.
     *
     * This is used to fix the metabox not refreshing issue in Gutenberg.
     *
     * @param RelationshipContext $context
     * @return bool
     */
    private function newRelationPost(RelationshipContext $context): bool
    {
        return true;
    }

    /**
     * Disconnects the current post with the one given in the request.
     *
     * @param RelationshipContext $context
     * @return bool
     */
    private function disconnectPost(RelationshipContext $context): bool
    {
        return $this->contentRelations->deleteRelation(
            [$context->remoteSiteId() => $context->remotePostId()],
            ContentRelations::CONTENT_TYPE_POST
        );
    }
}
