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
        $hasRemotePost = $context->hasRemotePost();
        $key = $this->key === MetaboxFields::FIELD_TITLE ? 'post_title' : 'post_name';
        $value = $hasRemotePost ? $context->remotePost()->{$key} : '';
        ?>
        <tr>
            <th scope="row">
                <label for="<?= esc_attr($id) ?>">
                    <?= esc_html($this->label($hasRemotePost)) ?>
                </label>
            </th>
            <td>
                <input
                    type="text"
                    name="<?= esc_attr($name) ?>"
                    id="<?= esc_attr($id) ?>"
                    class="large-text"
                    value="<?= esc_attr($value) ?>">
                <?php if (!$hasRemotePost) : ?>
                    <p class="description">
                        <?php
                        esc_html_e('If empty will be auto-generated', 'multilingualpress');
                        ?>
                    </p>
                <?php endif ?>
            </td>
        </tr>
        <?php
    }

    /**
     * @param bool $hasRemotePost
     * @return string
     */
    private function label(bool $hasRemotePost): string
    {
        if ($this->key === MetaboxFields::FIELD_TITLE) {
            return $hasRemotePost
                    ? __('Post Title:', 'multilingualpress')
                    : __('New Post Title:', 'multilingualpress');
        }

        return $hasRemotePost
            ? __('Post Slug:', 'multilingualpress')
            : __('New Post Slug:', 'multilingualpress');
    }
}
