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

use Inpsyde\MultilingualPress\Attachment;
use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxAction;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxTab;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\PanelView;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post;

class ProductMetaboxesBehaviorActivator
{
    const ALLOWED_POST_TYPES = [
        'product',
    ];

    /**
     * @var MetaboxFields
     */
    private $metaboxFields;

    /**
     * @var PanelView
     */
    private $panelView;

    /**
     * @var ActivePostTypes
     */
    private $activePostTypes;

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var Attachment\Copier
     */
    private $attachmentCopier;

    /**
     * @var PersistentAdminNotices
     */
    private $notice;

    /**
     * ProductMetaboxesActivator constructor
     *
     * @param MetaboxFields $metaboxFields
     * @param PanelView $panelView
     * @param ActivePostTypes $activePostTypes
     * @param ContentRelations $contentRelations
     * @param Attachment\Copier $attachmentCopier
     * @param PersistentAdminNotices $notice
     */
    public function __construct(
        MetaboxFields $metaboxFields,
        PanelView $panelView,
        ActivePostTypes $activePostTypes,
        ContentRelations $contentRelations,
        Attachment\Copier $attachmentCopier,
        PersistentAdminNotices $notice
    ) {

        $this->metaboxFields = $metaboxFields;
        $this->panelView = $panelView;
        $this->activePostTypes = $activePostTypes;
        $this->contentRelations = $contentRelations;
        $this->attachmentCopier = $attachmentCopier;
        $this->notice = $notice;
    }

    /**
     * @param array $tabs
     * @param Post\RelationshipContext $context
     * @return array
     */
    public function setupMetaboxFields(array $tabs, Post\RelationshipContext $context): array
    {
        $postType = $context->sourcePost()->post_type;
        if (!\in_array($postType, self::ALLOWED_POST_TYPES, true)) {
            return $tabs;
        }

        $this->removeTabExcerpt($tabs);

        return array_merge($tabs, $this->metaboxFields->allFieldsTabs());
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param Post\RelationshipContext $relationshipContext
     */
    public function renderPanels(
        MetaboxFieldsHelper $helper,
        Post\RelationshipContext $relationshipContext
    ) {

        $this->panelView->render($helper, $relationshipContext);
    }

    /**
     * @param Post\RelationshipContext $context
     * @param Request $request
     * @param PersistentAdminNotices $notice
     */
    public function saveMetaboxes(
        Post\RelationshipContext $context,
        Request $request,
        PersistentAdminNotices $notice
    ) {

        $productMetabox = new MetaboxAction(
            $context,
            $this->activePostTypes,
            $this->contentRelations,
            $this->attachmentCopier,
            $this->metaboxFields,
            $this->notice
        );
        $productMetabox->save($request, $notice);
    }

    /**
     * @param $tabs
     */
    private function removeTabExcerpt(array &$tabs)
    {
        /**
         * @var string $key
         * @var MetaboxTab $tab
         */
        foreach ($tabs as $key => $tab) {
            if ($tab->id() === 'tab-excerpt') {
                unset($tabs[$key]);
                break;
            }
        }
    }
}
