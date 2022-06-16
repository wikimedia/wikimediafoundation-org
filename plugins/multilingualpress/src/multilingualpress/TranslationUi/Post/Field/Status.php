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

class Status
{
    const FILTER_TRANSLATION_UI_POST_STATUSES = 'multilingualpress.translation_ui_post_statuses';

    /**
     * @var array
     */
    protected static $statues;

    /**
     * @param $value
     * @return string
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public static function sanitize($value): string
    {
        // phpcs:enable

        if (!is_scalar($value) || in_array((string)$value, static::statuses(), true)) {
            return 'draft';
        }

        return (string)$value;
    }

    /**
     * @return array
     */
    protected static function statuses(): array
    {
        if (is_array(static::$statues)) {
            return static::$statues;
        }

        $statues = (array)apply_filters(
            self::FILTER_TRANSLATION_UI_POST_STATUSES,
            [
                'draft',
                'pending',
                'publish',
                'future',
            ]
        );

        static::$statues = [];
        foreach ($statues as $status) {
            if (is_string($status)) {
                $object = get_post_status_object($status);
                $object and static::$statues[$status] = $object->label;
            }
        }

        return static::$statues;
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $id = $helper->fieldId(MetaboxFields::FIELD_STATUS);
        $name = $helper->fieldName(MetaboxFields::FIELD_STATUS);
        $current = 'draft';
        $label = __('New Post Status:', 'multilingualpress');
        $statuses = static::statuses();
        if ($context->hasRemotePost()) {
            $current = $context->remotePost()->post_status;
            $label = __('Post Status:', 'multilingualpress');
            $statuses = array_merge([
                'none' => esc_html__('Do not change', 'multilingualpress'),
            ], $statuses);
        }
        ?>
        <tr>
            <th scope="row">
                <label for="<?= esc_attr($id) ?>">
                    <?= esc_html($label) ?>
                </label>
            </th>
            <td>
                <select id="<?= esc_attr($id) ?>" name="<?= esc_attr($name) ?>">
                    <?php foreach ($statuses as $value => $label) : ?>
                        <option
                            value="<?= esc_attr($value) ?>"
                            <?= selected($value, $current) ?>>
                            <?= esc_html($label) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>
        <?php
    }
}
