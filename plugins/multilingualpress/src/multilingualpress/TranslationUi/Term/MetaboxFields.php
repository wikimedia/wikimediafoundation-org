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

class MetaboxFields
{
    const TAB_RELATION = 'tab-relation';
    const TAB_DATA = 'tab-data';

    const FIELD_RELATION = 'relationship';
    const FIELD_RELATION_NEW = 'new';
    const FIELD_RELATION_EXISTING = 'existing';
    const FIELD_RELATION_REMOVE = 'remove';
    const FIELD_RELATION_LEAVE = 'leave';
    const FIELD_RELATION_NOTHING = 'nothing';
    const FIELD_RELATION_SEARCH = 'search_term_id';
    const FIELD_NAME = 'remote-name';
    const FIELD_SLUG = 'remote-slug';
    const FIELD_DESCRIPTION = 'remote-description';
    const FIELD_PARENT = 'remote-parent';

    /**
     * @return array
     */
    public function allFieldsTabs(): array
    {
        return [
            new MetaboxTab(
                self::TAB_RELATION,
                _x('Relationship', 'translation term metabox', 'multilingualpress'),
                ...$this->relationFields()
            ),
            new MetaboxTab(
                self::TAB_DATA,
                _x('Term Data', 'translation term metabox', 'multilingualpress'),
                ...$this->dataFields()
            ),
        ];
    }

    /**
     * @return array
     */
    private function relationFields(): array
    {
        return [
            new MetaboxField(
                self::FIELD_RELATION,
                new Field\Relation(),
                [Field\Relation::class, 'sanitize']
            ),
        ];
    }

    /**
     * @return array
     */
    private function dataFields(): array
    {
        return [
            new MetaboxField(
                self::FIELD_NAME,
                new Field\Base(self::FIELD_NAME),
                'sanitize_text_field'
            ),
            new MetaboxField(
                self::FIELD_SLUG,
                new Field\Base(self::FIELD_SLUG),
                'sanitize_text_field'
            ),
            new MetaboxField(
                self::FIELD_PARENT,
                new Field\ParentTerm(),
                [Field\ParentTerm::class, 'sanitize']
            ),
            new MetaboxField(
                self::FIELD_DESCRIPTION,
                new Field\Description(),
                'wp_kses_post'
            ),
        ];
    }
}
