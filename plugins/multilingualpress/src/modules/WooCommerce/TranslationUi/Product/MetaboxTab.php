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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post;

/**
 * MultilingualPress MetaboxTab for Product
 */
final class MetaboxTab implements Post\MetaboxFillable
{
    const ACTION_BEFORE_METABOX_UI_PANEL = 'multilingualpress.before_metabox_panel';
    const ACTION_AFTER_METABOX_UI_PANEL = 'multilingualpress.after_metabox_panel';
    const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
    const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
    const FILTER_TRANSLATION_UI_SHOW_CONTENT = 'multilingualpress.translation_ui_show_content';

    /**
     * @var string
     */
    private $id;

    /**
     * @var MetaboxField[]
     */
    private $fields;

    /**
     * @var string
     */
    private $label;

    public function __construct(
        string $id,
        string $label,
        Post\PostMetaboxField ...$fields
    ) {

        $this->id = $id;
        $this->label = $label;
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * @inheritdoc
     */
    public function enabled(Post\RelationshipContext $relationshipContext): bool
    {
        $enabled = true;

        /**
         * Enable/Disable Metabox
         *
         * @param bool $enabled to enable or not.
         * @param string $id The id of the tab.
         * @param Post\RelationshipContext The post context instance.
         */
        $enabled = (bool)apply_filters(
            self::FILTER_TRANSLATION_UI_SHOW_CONTENT,
            $enabled,
            $this->id,
            $relationshipContext
        );

        /**
         * Enable/Disable Metabox for a specific id
         *
         * @param bool $enabled to enable or not.
         * @param Post\RelationshipContext The post context instance.
         */
        return (bool)apply_filters(
            self::FILTER_TRANSLATION_UI_SHOW_CONTENT . "_{$this->id}",
            $enabled,
            $relationshipContext
        );
    }

    /**
     * @inheritdoc
     */
    public function render(
        MetaboxFieldsHelper $helper,
        Post\RelationshipContext $relationshipContext
    ) {

        if (!$this->enabled($relationshipContext)) {
            return;
        }
        ?>
        <div class="wp-tab-panel"
             id="<?= esc_attr($helper->fieldId($this->id())) ?>"
             data-tab-id="<?= esc_attr($this->id()) ?>"
        >
            <?php
            switch_to_blog($relationshipContext->remoteSiteId());
            do_action(self::ACTION_BEFORE_METABOX_UI_PANEL, $helper, $relationshipContext);
            $this->renderFields($helper, $relationshipContext);
            do_action(self::ACTION_AFTER_METABOX_UI_PANEL, $helper, $relationshipContext);
            restore_current_blog();
            ?>
        </div>
        <?php
    }

    /**
     * Render MultilingualPress custom fields for product.
     *
     * @param MetaboxFieldsHelper $helper
     * @param Post\RelationshipContext $relationshipContext
     */
    private function renderFields(
        MetaboxFieldsHelper $helper,
        Post\RelationshipContext $relationshipContext
    ) {

        $id = $this->id();

        if (!$this->fields) {
            return;
        }
        ?>
        <div class="wp-tab-panel-inner">
            <table class="form-table">
                <tbody>
                <?php
                do_action(self::ACTION_BEFORE_TRANSLATION_UI_TAB . "_{$id}_fields");
                foreach ($this->fields() as $field) {
                    $field instanceof Post\MetaboxField and $field->render(
                        $helper,
                        $relationshipContext
                    );
                }
                do_action(self::ACTION_AFTER_TRANSLATION_UI_TAB . "_{$id}_fields");
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
