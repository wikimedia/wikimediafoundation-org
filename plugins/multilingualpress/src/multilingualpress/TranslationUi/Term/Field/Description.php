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

namespace Inpsyde\MultilingualPress\TranslationUi\Term\Field;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields;

class Description
{

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $id = $helper->fieldId(MetaboxFields::FIELD_DESCRIPTION);
        $name = $helper->fieldName(MetaboxFields::FIELD_DESCRIPTION);
        $value = '';
        $label = __('New Term Description:', 'multilingualpress');
        if ($context->hasRemoteTerm()) {
            $value = $context->remoteTerm()->description;
            $label = __('Term Description:', 'multilingualpress');
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
