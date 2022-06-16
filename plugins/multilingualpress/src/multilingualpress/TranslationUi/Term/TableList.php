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

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\NetworkState;

use function Inpsyde\MultilingualPress\siteLanguageTag;

class TableList
{
    const RELATION_TYPE = 'term';
    const EDIT_TRANSLATIONS_COLUMN_NAME = 'translations';
    const FILTER_SITE_LANGUAGE_TAG = 'multilingualpress.site_language_tag';

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * TableList constructor.
     * @param ContentRelations $contentRelations
     */
    public function __construct(ContentRelations $contentRelations)
    {
        $this->contentRelations = $contentRelations;
    }

    /**
     * @param array $postsColumns
     * @return array
     */
    public function editTranslationColumns(array $postsColumns): array
    {
        $postsColumns[self::EDIT_TRANSLATIONS_COLUMN_NAME] = esc_html__(
            'Translations',
            'multilingualpress'
        );

        return $postsColumns;
    }

    /**
     * @param string $content
     * @param string $columnName
     * @param int $termId
     * @return void
     */
    public function editTranslationLinks(string $content, string $columnName, int $termId)
    {
        if ($termId < 1) {
            return;
        }
        if ($columnName !== self::EDIT_TRANSLATIONS_COLUMN_NAME) {
            return;
        }

        $translations = [];
        $currentSiteId = get_current_blog_id();
        $relations = $this->contentRelations->relations(
            $currentSiteId,
            $termId,
            self::RELATION_TYPE
        );
        unset($relations[$currentSiteId]);

        if (!$relations) {
            return;
        }

        $networkState = NetworkState::create();
        foreach ($relations as $siteId => $contentId) {
            switch_to_blog($siteId);

            /**
             * Filter Site Language Tag
             *
             * @param string $siteLanguageTag
             * @param int $siteId
             * @param array $relations
             */
            $siteLanguageTag = apply_filters(
                self::FILTER_SITE_LANGUAGE_TAG,
                siteLanguageTag($siteId),
                $siteId,
                $relations
            );

            $term = get_term($contentId);
            if (!$term instanceof \WP_Term) {
                continue;
            }

            $siteLanguageTag and $translations[] = sprintf(
                '<a href="%1$s">%2$s</a>',
                esc_url(get_edit_term_link($contentId, $term->taxonomy)),
                $siteLanguageTag
            );
        }
        $networkState->restore();

        $translationLinks = implode(
            '<span class="mlp-table-list-relations-divide"></span>',
            $translations
        );

        echo wp_kses_post($translationLinks);
    }
}
