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

namespace Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post\Field;

use Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class CopyACFFields
{
    const FILTER_COPY_ACF_FIELDS_IS_CHECKED = 'multilingualpress.copy_custom_fields_is_checked';

    /**
     * @param $value
     * @return string
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public static function sanitize($value): string
    {
        // phpcs:enable

        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '';
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $id = $helper->fieldId(MetaboxFields::FIELD_COPY_ACF_FIELDS);
        $name = $helper->fieldName(MetaboxFields::FIELD_COPY_ACF_FIELDS);

        /**
         * Filter if the input should be prechecked
         *
         * @param bool $checked
         */
        $checked = (bool)apply_filters(
            self::FILTER_COPY_ACF_FIELDS_IS_CHECKED,
            false
        );
        ?>
        <tr>
            <th scope="row">
                <?php esc_html_e('Copy ACF Fields', 'multilingualpress') ?>
            </th>
            <td>
                <label for="<?= esc_attr($id) ?>">
                    <input
                        type="checkbox"
                        name="<?= esc_attr($name) ?>"
                        value="1"
                        id="<?= esc_attr($id) ?>"
                        <?php checked($checked) ?>
                    />
                    <?php
                    esc_html_e(
                        'Overwrites the custom field values on translated post with the values of custom fields of source post .',
                        'multilingualpress'
                    );
                    ?>
                </label>
                <p>
                    <small>
                        <?php
                        esc_html_e(
                            'Please note, the ACF fields should be created in remote site for this option to work',
                            'multilingualpress'
                        );
                        ?>
                    </small>
                </p>
            </td>
        </tr>
        <?php
    }
}
