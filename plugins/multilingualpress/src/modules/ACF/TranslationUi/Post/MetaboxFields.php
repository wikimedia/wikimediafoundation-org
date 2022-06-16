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

namespace Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post;

use Inpsyde\MultilingualPress\TranslationUi\Post;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxTab;

/**
 * MultilingualPress ACF Metabox Fields
 */
class MetaboxFields
{

    const TAB = 'tab-custom-fields';
    const FIELD_COPY_ACF_FIELDS = 'remote-acf-fields-copy';

    /**
     * Retrieve all fields for the ACF metabox tab.
     *
     * @return MetaboxTab[]
     */

    public function allFieldsTabs(): array
    {
        return [
            new MetaboxTab(
                MetaboxFields::TAB,
                _x('ACF', 'translation post metabox', 'multilingualpress'),
                new Post\MetaboxField(
                    MetaboxFields::FIELD_COPY_ACF_FIELDS,
                    new Field\CopyACFFields()
                )
            ),
        ];
    }
}
