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

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

class MetaboxField
{
    const ACTION_AFTER_TRANSLATION_UI_FIELD = 'multilingualpress.after_translation_ui_field';
    const ACTION_BEFORE_TRANSLATION_UI_FIELD = 'multilingualpress.before_translation_ui_field';
    const FILTER_TRANSLATION_UI_SHOW_FIELD = 'multilingualpress.translation_ui_show_field';

    /**
     * @var string
     */
    private $key;

    /**
     * @var callable
     */
    private $renderCallback;
    /**
     * @var callable
     */
    private $sanitizer;

    /**
     * @param string $key
     * @param callable $renderCallback
     * @param callable|null $sanitizer
     */
    public function __construct(string $key, callable $renderCallback, callable $sanitizer = null)
    {
        $this->key = $key;
        $this->renderCallback = $renderCallback;
        $this->sanitizer = $sanitizer;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     */
    public function render(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $enabled = $this->enabled($relationshipContext);

        do_action(
            self::ACTION_BEFORE_TRANSLATION_UI_FIELD . "_{$this->key()}",
            $relationshipContext,
            $enabled
        );

        if ($enabled) {
            ($this->renderCallback)($helper, $relationshipContext);
        }

        do_action(
            self::ACTION_AFTER_TRANSLATION_UI_FIELD . "_{$this->key()}",
            $relationshipContext,
            $enabled
        );
    }

    /**
     * @param Request $request
     * @param MetaboxFieldsHelper $helper
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function requestValue(Request $request, MetaboxFieldsHelper $helper)
    {
        // phpcs:enable

        $value = $helper->fieldRequestValue($request, $this->key());
        if (!$this->sanitizer) {
            return $value;
        }

        return ($this->sanitizer)($value);
    }

    /**
     * @param RelationshipContext $relationshipContext
     * @return bool
     */
    public function enabled(RelationshipContext $relationshipContext): bool
    {
        $enabled = (bool)apply_filters(
            self::FILTER_TRANSLATION_UI_SHOW_FIELD,
            true,
            $this,
            $relationshipContext
        );

        return (bool)apply_filters(
            self::FILTER_TRANSLATION_UI_SHOW_FIELD . "_{$this->key()}",
            $enabled,
            $relationshipContext
        );
    }
}
