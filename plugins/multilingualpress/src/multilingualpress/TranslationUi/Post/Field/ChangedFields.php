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

namespace Inpsyde\MultilingualPress\TranslationUi\Post\Field;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback;

class ChangedFields implements RenderCallback
{
    protected const FILTER_FIELD_FIELDS_ARE_CHANGED = 'multilingualpress.field_changed_fields';

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $name = $helper->fieldName(MetaboxFields::FIELD_CHANGED_FIELDS);
        ?>
            <input type="hidden" class="changed-fields" name="<?= esc_attr($name) ?>" value="">
        <?php
    }
}
