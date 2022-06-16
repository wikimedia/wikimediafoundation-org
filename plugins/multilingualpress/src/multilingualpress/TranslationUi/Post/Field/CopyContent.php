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

class CopyContent
{
    const FILTER_COPY_CONTENT_IS_CHECKED = 'multilingualpress.copy_content_is_checked';

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
        $id = $helper->fieldId(MetaboxFields::FIELD_COPY_CONTENT);
        $name = $helper->fieldName(MetaboxFields::FIELD_COPY_CONTENT);

        /**
         * Filter if the input should be prechecked
         *
         * @param bool $checked
         */
        $checked = (bool)apply_filters(
            self::FILTER_COPY_CONTENT_IS_CHECKED,
            false
        );
        ?>
        <tr>
            <th scope="row">
                <label for="<?= esc_attr($id) ?>">
                    <?php esc_html_e('Copy source content', 'multilingualpress') ?>
                </label>
            </th>
            <td>
                <input
                    type="checkbox"
                    name="<?= esc_attr($name) ?>"
                    value="1"
                    id="<?= esc_attr($id) ?>"
                    <?php checked($checked) ?>
                />
                <?php
                esc_html_e(
                    'Overwrites content on translated post with the content of source post.',
                    'multilingualpress'
                );
                ?>
            </td>
        </tr>
        <?php
    }
}
