<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\TranslationUi\Post;

class MetaboxFields
{
    const TAB_BASE = 'tab-base';
    const TAB_EXCERPT = 'tab-excerpt';
    const TAB_MORE = 'tab-more';
    const TAB_RELATION = 'tab-relation';
    const TAB_TAXONOMIES = 'tab-taxonomies';

    const FIELD_RELATION = 'relationship';
    const FIELD_RELATION_NEW = 'new';
    const FIELD_RELATION_EXISTING = 'existing';
    const FIELD_RELATION_REMOVE = 'remove';
    const FIELD_RELATION_LEAVE = 'leave';
    const FIELD_RELATION_NOTHING = 'nothing';
    const FIELD_RELATION_SEARCH = 'search_post_id';
    const FIELD_EXCERPT = 'remote-excerpt';
    const FIELD_TITLE = 'remote-title';
    const FIELD_SLUG = 'remote-slug';
    const FIELD_STATUS = 'remote-status';
    const FIELD_COPY_FEATURED = 'remote-thumbnail-copy';
    const FIELD_COPY_CONTENT = 'remote-content-copy';
    const FIELD_COPY_TAXONOMIES = 'remote-taxonomies-copy';
    const FIELD_TAXONOMIES = 'remote-taxonomies';
    const FIELD_TAXONOMY_SLUGS = 'remote-taxonomy-slugs';
    const FIELD_EDIT_LINK = 'edit-link';

    const FILTER_TAXONOMIES_AND_TERMS_OF = 'multilingualpress.taxonomies_and_terms_of';

    /**
     * Get all existing taxonomies for the given post, including all existing terms.
     *
     * @param \WP_Post $post
     * @return \stdClass[]
     */
    public static function taxonomiesAndTermsOf(\WP_Post $post): array
    {
        /** @var \WP_Taxonomy[] $taxonomies */
        $taxonomies = get_object_taxonomies($post);

        /**
         * Filter Taxonomies for post
         *
         * @param array $taxonomies A list of taxonomies associated to a post.
         * @param \WP_Post $post The current post object with which the taxonomies are related.
         */
        $taxonomies = apply_filters(
            self::FILTER_TAXONOMIES_AND_TERMS_OF,
            $taxonomies,
            $post
        );

        if (!$taxonomies) {
            return [];
        }

        /** @var \WP_Term[] $allTerms */
        $allTerms = get_terms(['taxonomy' => $taxonomies, 'hide_empty' => false]);
        if (!$allTerms || is_wp_error($allTerms)) {
            return [];
        }

        $output = [];
        foreach ($allTerms as $term) {
            $sameTax = array_key_exists($term->taxonomy, $output);
            $taxonomy = $sameTax ? $output[$term->taxonomy]->object : get_taxonomy($term->taxonomy);

            if (!$taxonomy
                || !$taxonomy->show_ui
                || !current_user_can($taxonomy->cap->assign_terms, $taxonomy->name)
            ) {
                continue;
            }

            if (!$sameTax) {
                $output[$term->taxonomy] = (object)['object' => $taxonomy, 'terms' => []];
            }

            $output[$term->taxonomy]->terms[] = $term;
        }

        ksort($output);

        return $output;
    }

    /**
     * @param RelationshipContext $context
     * @return array
     */
    public function allFieldsTabs(RelationshipContext $context): array
    {
        $remotePost = $context->remotePost();
        $hasRemotePost = $context->hasRemotePost() && $remotePost instanceof \WP_Post;
        $type = $hasRemotePost
            ? $remotePost->post_type
            : $context->sourcePost()->post_type;

        $tabs = [
            new MetaboxTab(
                self::TAB_RELATION,
                _x('Relationship', 'translation post metabox', 'multilingualpress'),
                ...$this->relationFields()
            ),
        ];
        if (post_type_supports($type, 'title')) {
            $label = post_type_supports($type, 'editor')
                ? _x('Title and Content', 'translation post metabox', 'multilingualpress')
                : _x('Title', 'translation post metabox', 'multilingualpress');

            $tabs[] = new MetaboxTab(
                self::TAB_BASE,
                $label,
                ...$this->baseFields($context)
            );
        }

        if (post_type_supports($type, 'excerpt')) {
            $tabs[] = new MetaboxTab(
                self::TAB_EXCERPT,
                _x('Excerpt', 'translation post metabox', 'multilingualpress'),
                ...$this->excerptFields()
            );
        }

        $tabs[] = new MetaboxTab(
            self::TAB_MORE,
            _x('Advanced', 'translation post metabox', 'multilingualpress'),
            ...$this->moreFields($context)
        );

        $tabs[] = new MetaboxTab(
            self::TAB_TAXONOMIES,
            _x('Taxonomies', 'translation post metabox', 'multilingualpress'),
            ...$this->taxonomiesFields($context)
        );

        return $tabs;
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
     * @param RelationshipContext $context
     * @return MetaboxField[]
     */
    private function baseFields(RelationshipContext $context): array
    {
        $fields = [
            new MetaboxField(
                self::FIELD_TITLE,
                new Field\Base(self::FIELD_TITLE),
                'sanitize_text_field'
            ),
        ];

        $postType = $context->remotePost()
            ? $context->remotePost()->post_type
            : $context->sourcePost()->post_type;

        if (post_type_supports($postType, 'editor')) {
            $fields[] = new MetaboxField(
                self::FIELD_COPY_CONTENT,
                new Field\CopyContent(),
                [Field\CopyContent::class, 'sanitize']
            );
        }

        return $fields;
    }

    /**
     * @return MetaboxField[]
     */
    private function excerptFields(): array
    {
        return [
            new MetaboxField(
                self::FIELD_EXCERPT,
                new Field\Excerpt(),
                'wp_kses_post'
            ),
        ];
    }

    /**
     * @param RelationshipContext $context
     * @return MetaboxField[]
     */
    private function moreFields(RelationshipContext $context): array
    {
        $fields = [
            new MetaboxField(
                self::FIELD_SLUG,
                new Field\Base(self::FIELD_SLUG),
                'sanitize_text_field'
            ),
            new MetaboxField(
                self::FIELD_STATUS,
                new Field\Status(),
                [Field\Status::class, 'sanitize']
            ),
        ];

        $hasRemotePost = $context->hasRemotePost();
        $postType = $hasRemotePost
            ? $context->remotePost()->post_type
            : $context->sourcePost()->post_type;

        if (post_type_supports($postType, 'thumbnail')) {
            $fields[] = new MetaboxField(
                self::FIELD_COPY_FEATURED,
                new Field\CopyFeaturedImage(),
                [Field\CopyFeaturedImage::class, 'sanitize']
            );
        }

        if ($hasRemotePost) {
            $fields[] = new MetaboxField(self::FIELD_EDIT_LINK, new Field\EditLink());
        }

        return $fields;
    }

    /**
     * @param RelationshipContext $context
     * @return MetaboxField[]
     */
    private function taxonomiesFields(RelationshipContext $context): array
    {
        $post = $context->remotePost();
        if (!$post) {
            $post = new \WP_Post((object)['post_type' => $context->sourcePost()->post_type]);
        }

        $taxonomies = self::taxonomiesAndTermsOf($post);

        if (!$taxonomies) {
            return [];
        }

        $fields = [
            new MetaboxField(
                self::FIELD_COPY_TAXONOMIES,
                new Field\CopyTaxonomies(),
                [Field\CopyTaxonomies::class, 'sanitize']
            ),
        ];

        foreach ($taxonomies as $taxonomyData) {
            if (!$taxonomyData->terms) {
                continue;
            }
            $fields[] = new MetaboxField(
                self::FIELD_TAXONOMIES,
                new Field\Taxonomies($taxonomyData->object, ...$taxonomyData->terms),
                [Field\Taxonomies::class, 'sanitize']
            );
        }

        $fields[] = new MetaboxField(
            self::FIELD_TAXONOMY_SLUGS,
            new Field\TaxonomySlugs(),
            [Field\TaxonomySlugs::class, 'sanitize']
        );

        return $fields;
    }
}
