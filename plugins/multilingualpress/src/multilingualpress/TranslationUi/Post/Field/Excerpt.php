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

class Excerpt
{

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $id = $helper->fieldId(MetaboxFields::FIELD_EXCERPT);
        $name = $helper->fieldName(MetaboxFields::FIELD_EXCERPT);
        $value = '';
        $label = __('New Post Excerpt:', 'multilingualpress');
        if ($context->hasRemotePost()) {
            $value = $context->remotePost()->post_excerpt;
            $label = __('Post Excerpt:', 'multilingualpress');
        }

        ?>
        <tr>
            <th scope="row">
                <label for="<?= esc_attr($id) ?>">
                    <?= esc_html($label) ?>
                </label>
            </th>
            <td>
               <textarea
                   id="<?= esc_attr($id) ?>"
                   name="<?= esc_attr($name) ?>"
                   rows="3"
                   class="large-text"><?= esc_textarea($value) ?></textarea>
            </td>
        </tr>
        <?php
    }
}
