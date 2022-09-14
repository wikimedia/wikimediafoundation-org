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

namespace Inpsyde\MultilingualPress\TranslationUi\Term\Ajax;

use Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies;
use Inpsyde\MultilingualPress\Framework\Entity;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox as FrameworkMetabox;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\Term\Metabox;
use Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipPermission;

class RelationshipUpdater
{
    const ACTION = 'multilingualpress_update_term_relationship';
    const TASK_PARAM = 'task';

    const TASK_METHOD_MAP = [
        MetaboxFields::FIELD_RELATION_EXISTING => 'connectExistingTerm',
        MetaboxFields::FIELD_RELATION_REMOVE => 'disconnectTerm',
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
     * @var ActiveTaxonomies
     */
    private $taxonomies;

    /**
     * @var RelationshipPermission
     */
    private $relationshipPermission;

    /**
     * @param Request $request
     * @param ContextBuilder $contextBuilder
     * @param ContentRelations $contentRelations
     * @param ActiveTaxonomies $taxonomies
     * @param RelationshipPermission $relationshipPermission
     */
    public function __construct(
        Request $request,
        ContextBuilder $contextBuilder,
        ContentRelations $contentRelations,
        ActiveTaxonomies $taxonomies,
        RelationshipPermission $relationshipPermission
    ) {

        $this->request = $request;
        $this->contextBuilder = $contextBuilder;
        $this->contentRelations = $contentRelations;
        $this->taxonomies = $taxonomies;
        $this->relationshipPermission = $relationshipPermission;
    }

    /**
     * Handle AJAX request.
     *
     * @see RelationshipUpdater::connectExistingTerm()
     * @see RelationshipUpdater::disconnectTerm()
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
            $term = $context->sourceTerm();
            $metabox = new Metabox(
                $context->sourceSiteId(),
                $remoteSiteId,
                $this->taxonomies,
                $this->contentRelations,
                $this->relationshipPermission
            );

            $info = $metabox->createInfo(FrameworkMetabox::SHOW, new Entity($term));

            switch_to_blog($remoteSiteId);
            ob_start();
            $metabox->viewForTerm($term)->render($info);
            $rendered = ob_get_clean();
            restore_current_blog();

            wp_send_json_success($rendered);
        }

        wp_send_json_error($this->lastError);
    }

    /**
     * Connects the current term with an existing remote one.
     *
     * @param RelationshipContext $context
     * @return bool
     */
    private function connectExistingTerm(RelationshipContext $context): bool
    {
        $contentIds = [$context->sourceSiteId() => $context->sourceTermId()];

        $relationshipId = $this->contentRelations->relationshipId(
            $contentIds,
            ContentRelations::CONTENT_TYPE_TERM
        );

        if (!$relationshipId) {
            $relationshipId = $this->contentRelations->relationshipId(
                [$context->remoteSiteId() => $context->remoteTermId()],
                ContentRelations::CONTENT_TYPE_TERM,
                true
            );
        }

        if (!$relationshipId) {
            $this->lastError = 'Error saving relation';

            return false;
        }

        $contentIds[$context->remoteSiteId()] = $context->remoteTermId();

        foreach ($contentIds as $siteId => $termId) {
            if (!$this->contentRelations->saveRelation($relationshipId, $siteId, $termId)) {
                $this->lastError = "Error saving relation for site {$siteId} and term {$termId}.";

                return false;
            }
        }

        return true;
    }

    /**
     * Disconnects the current term with the one given in the request.
     *
     * @param RelationshipContext $context
     * @return bool
     */
    private function disconnectTerm(RelationshipContext $context): bool
    {
        return $this->contentRelations->deleteRelation(
            [$context->remoteSiteId() => $context->remoteTermId()],
            ContentRelations::CONTENT_TYPE_TERM
        );
    }
}
