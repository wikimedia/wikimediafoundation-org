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

use Inpsyde\MultilingualPress\Framework\Admin\Metabox;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Term\Metabox as Box;

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
     * @param MetaboxFields $fields
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     */
    public function __construct(
        MetaboxFields $fields,
        MetaboxFieldsHelper $helper,
        RelationshipContext $relationshipContext
    ) {

        $this->fields = $fields;
        $this->helper = $helper;
        $this->relationshipContext = $relationshipContext;
    }

    /**
     * @inheritdoc
     */
    public function render(Metabox\Info $info)
    {
        $tabFields = apply_filters(
            Box::HOOK_PREFIX . 'tabs',
            $this->fields->allFieldsTabs()
        );

        ?>
        <div
            class="mlp-translation-metabox mlp-translation-metabox--term"
            <?php $this->boxDataAttributes() ?>
        >

            <?php $this->relationshipContext->renderFields($this->helper) ?>

            <ul class="nav-tab-wrapper wp-clearfix">
                <?php
                /** @var MetaboxTab $tab */
                foreach ($tabFields as $tab) {
                    if (
                        $tab instanceof MetaboxTab
                        && $tab->enabled($this->relationshipContext)
                    ) {
                        $this->renderTabAnchor($tab);
                    }
                }
                ?>
            </ul>
            <?php
            foreach ($tabFields as $tab) {
                if ($tab instanceof MetaboxTab) {
                    /** @var MetaboxTab $tab */
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
        $taxonomy = $this->relationshipContext->hasRemoteTerm()
            ? $this->relationshipContext->remoteTerm()->taxonomy
            : $this->relationshipContext->sourceTerm()->taxonomy;

        ?>
        data-source-site="<?= esc_attr((string)$this->relationshipContext->sourceSiteId()) ?>"
        data-source-term="<?= esc_attr((string)$this->relationshipContext->sourceTermId()) ?>"
        data-remote-site="<?= esc_attr((string)$this->relationshipContext->remoteSiteId()) ?>"
        data-remote-term="<?= esc_attr((string)$this->relationshipContext->remoteTermId()) ?>"
        data-taxonomy="<?= esc_attr($taxonomy) ?>"
        data-remote-link="<?= esc_attr($this->remoteTermUrl()) ?>"
        data-remote-link-label="<?= esc_attr__('Edit', 'multilingualpress') ?>"
        <?php
    }

    /**
     * @param MetaboxTab $tab
     */
    private function renderTabAnchor(MetaboxTab $tab)
    {
        $tabId = $tab->id();
        $label = (string)apply_filters(
            "multilingualpress.translation_term_metabox_tab_{$tabId}_anchor",
            $tab->label()
        );

        printf(
            '<li class="nav-tab" id="tab-anchor-%1$s"><a href="#%1$s">%2$s</a></li>',
            esc_attr($this->helper->fieldId($tabId)),
            esc_html($label)
        );
    }

    /**
     * Retrieve the edit link for the remote term
     *
     * @return string
     */
    private function remoteTermUrl(): string
    {
        $remoteTerm = $this->relationshipContext->remoteTerm();

        if (!$remoteTerm) {
            return '';
        }

        return (string)get_edit_term_link($remoteTerm->term_id, $remoteTerm->taxonomy);
    }
}
