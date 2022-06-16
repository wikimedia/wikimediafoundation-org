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

class Base
{
    /**
     * @var string
     */
    private $key;

    /**
     * Relation constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $id = $helper->fieldId($this->key);
        $name = $helper->fieldName($this->key);
        $hasRemoteTerm = $context->hasRemoteTerm();
        $key = $this->key === MetaboxFields::FIELD_NAME ? 'name' : 'slug';
        $value = $hasRemoteTerm ? $context->remoteTerm()->{$key} : '';
        ?>
        <tr>
            <th scope="row">
                <label for="<?= esc_attr($id) ?>">
                    <?= esc_html($this->label($hasRemoteTerm)) ?>
                </label>
            </th>
            <td>
                <input
                    type="text"
                    name="<?= esc_attr($name) ?>"
                    id="<?= esc_attr($id) ?>"
                    class="large-text"
                    value="<?= esc_attr($value) ?>">
            </td>
        </tr>
        <?php
    }

    /**
     * @param bool $hasRemoteTerm
     * @return string
     */
    private function label(bool $hasRemoteTerm): string
    {
        if ($this->key === MetaboxFields::FIELD_NAME) {
            return $hasRemoteTerm
                    ? __('Term Name:', 'multilingualpress')
                    : __('New Term Name:', 'multilingualpress');
        }

        return $hasRemoteTerm
            ? __('Term Slug:', 'multilingualpress')
            : __('New Term Slug:', 'multilingualpress');
    }
}
