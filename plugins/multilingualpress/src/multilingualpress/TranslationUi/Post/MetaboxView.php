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

use Inpsyde\MultilingualPress\Framework\Admin\Metabox;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\Field\ChangedFields;
use Inpsyde\MultilingualPress\TranslationUi\Post\Metabox as Box;

final class MetaboxView implements Metabox\View
{
    /**
     * @var MetaboxFields
     */
    private $fields;

    /**
     * @var MetaboxFieldsHelper
     */
    private $helper;

    /**
     * @var RelationshipContext
     */
    private $relationshipContext;

    /**
     * @var ChangedFields
     */
    private $fieldsAreChangedInput;

    /**
     * @param MetaboxFields $fields
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @param ChangedFields $fieldsAreChangedInput
     */
    public function __construct(
        MetaboxFields $fields,
        MetaboxFieldsHelper $helper,
        RelationshipContext $relationshipContext,
        ChangedFields $fieldsAreChangedInput
    ) {

        $this->fields = $fields;
        $this->helper = $helper;
        $this->relationshipContext = $relationshipContext;
        $this->fieldsAreChangedInput = $fieldsAreChangedInput;
    }

    /**
     * @inheritdoc
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function render(Metabox\Info $info)
    {
        // phpcs:enable

        $remotePostIsTrashed = $this->relationshipContext->hasRemotePost()
            && $this->relationshipContext->remotePost()->post_status === 'trash';

        $tabFields = apply_filters(
            Box::HOOK_PREFIX . 'tabs',
            $this->fields->allFieldsTabs($this->relationshipContext),
            $this->relationshipContext
        );

        if ($remotePostIsTrashed) {
            $tabFields = array_filter(
                $tabFields,
                static function (MetaboxFillable $tab): bool {
                    return $tab->id() === MetaboxFields::TAB_RELATION;
                }
            );
            $this->renderTrashedMessage();
        }

        ?>
        <div
            class="mlp-translation-metabox mlp-translation-metabox--post"
            <?php $this->boxDataAttributes() ?>
        >

            <?php $this->relationshipContext->renderFields($this->helper) ?>
            <?php ($this->fieldsAreChangedInput)($this->helper, $this->relationshipContext); ?>

            <ul class="nav-tab-wrapper wp-clearfix">
                <?php
                /** @var MetaboxFillable $tab */
                foreach ($tabFields as $tab) {
                    if (
                        $tab instanceof MetaboxFillable
                        && $tab->enabled($this->relationshipContext)
                    ) {
                        $this->renderTabAnchor($tab);
                    }
                }
                ?>
            </ul>
            <?php
            foreach ($tabFields as $tab) {
                if ($tab instanceof MetaboxFillable) {
                    /** @var MetaboxFillable $tab */
                    $enabled = $tab->enabled($this->relationshipContext);
                    $tabId = $tab->id();
                    do_action(Box::HOOK_PREFIX . "before_tab_{$tabId}", $enabled);
                    $enabled and $tab->render($this->helper, $this->relationshipContext);
                    do_action(Box::HOOK_PREFIX . "after_tab_{$tabId}", $enabled);
                }
            }
            ?>
        </div>
        <?php
    }

    /**
     * @return void
     */
    private function boxDataAttributes()
    {
        $postType = $this->relationshipContext->hasRemotePost()
            ? $this->relationshipContext->remotePost()->post_type
            : $this->relationshipContext->sourcePost()->post_type;

        ?>
        data-source-site="<?= esc_attr((string)$this->relationshipContext->sourceSiteId()) ?>"
        data-source-post="<?= esc_attr((string)$this->relationshipContext->sourcePostId()) ?>"
        data-remote-site="<?= esc_attr((string)$this->relationshipContext->remoteSiteId()) ?>"
        data-remote-post="<?= esc_attr((string)$this->relationshipContext->remotePostId()) ?>"
        data-post-type="<?= esc_attr($postType) ?>"
        data-remote-link="<?= esc_attr($this->remotePostUrl()) ?>"
        data-remote-link-label="<?= esc_attr__('Edit', 'multilingualpress') ?>"
        <?php
    }

    /**
     * @param MetaboxFillable $tab
     */
    private function renderTabAnchor(MetaboxFillable $tab)
    {
        $tabId = $tab->id();
        $label = (string)apply_filters(
            "multilingualpress.translation_post_metabox_tab_{$tabId}_anchor",
            $tab->label()
        );

        printf(
            '<li class="nav-tab" id="tab-anchor-%1$s"><a href="#%1$s">%2$s</a></li>',
            esc_attr($this->helper->fieldId($tabId)),
            esc_html($label)
        );
    }

    /**
     * @return void
     */
    private function renderTrashedMessage()
    {
        ?>
        <div class="mlp-warning">
            <p>
                <?php
                esc_html_e(
                    'The currently connected translation post is trashed.',
                    'multilingualpress'
                );
                print ' ';
                esc_html_e(
                    'Edit the relationship or no further editing will be possible.',
                    'multilingualpress'
                );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Retrieve the post edit link for the remote post
     *
     * @return string
     */
    private function remotePostUrl(): string
    {
        $remotePost = $this->relationshipContext->remotePost();
        if (!$remotePost) {
            return '';
        }

        return get_edit_post_link($remotePost->ID) ?: '';
    }
}
