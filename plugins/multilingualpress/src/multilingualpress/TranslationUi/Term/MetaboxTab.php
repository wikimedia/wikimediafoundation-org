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

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

class MetaboxTab
{
    const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
    const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
    const FILTER_TRANSLATION_UI_SHOW_TAB = 'multilingualpress.translation_ui_show_tab';

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

    /**
     * @param string $id
     * @param string $label
     * @param MetaboxField[] ...$fields
     */
    public function __construct(string $id, string $label, MetaboxField ...$fields)
    {
        $this->id = $id;
        $this->fields = $fields;
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @return MetaboxField[]
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * @param RelationshipContext $relationshipContext
     * @return bool
     */
    public function enabled(RelationshipContext $relationshipContext): bool
    {
        if (!$this->fields) {
            return false;
        }

        $enabled = (bool)apply_filters(
            self::FILTER_TRANSLATION_UI_SHOW_TAB,
            true,
            $this->id,
            $relationshipContext
        );

        return (bool)apply_filters(
            self::FILTER_TRANSLATION_UI_SHOW_TAB . "_{$this->id}",
            $enabled,
            $relationshipContext
        );
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     */
    public function render(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        if (!$this->enabled($relationshipContext)) {
            return;
        }

        $id = $this->id();
        ?>
        <div class="wp-tab-panel"
             id="<?= esc_attr($helper->fieldId($this->id())) ?>"
             data-tab-id="<?= esc_attr($this->id()) ?>"
        >
            <table class="form-table <?= sanitize_html_class($this->id()) ?>">
                <tbody>
                <?php
                do_action(self::ACTION_BEFORE_TRANSLATION_UI_TAB . "_{$id}_fields");
                foreach ($this->fields() as $field) {
                    $field->render($helper, $relationshipContext);
                }
                do_action(self::ACTION_AFTER_TRANSLATION_UI_TAB . "_{$id}_fields");
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
