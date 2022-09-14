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

namespace Inpsyde\MultilingualPress\TranslationUi\Post;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

use function Inpsyde\MultilingualPress\combineAtts;

/**
 * Relationship context data object.
 */
class RelationshipContext
{
    const REMOTE_POST_ID = 'remote_post_id';
    const REMOTE_SITE_ID = 'remote_site_id';
    const SOURCE_POST_ID = 'source_post_id';
    const SOURCE_SITE_ID = 'source_site_id';

    const DEFAULTS = [
        self::REMOTE_POST_ID => 0,
        self::REMOTE_SITE_ID => 0,
        self::SOURCE_POST_ID => 0,
        self::SOURCE_SITE_ID => 0,
    ];

    /**
     * @var \WP_Post[]
     */
    private $posts = [];

    /**
     * @var array
     */
    private $data;

    /**
     * Returns a new context object, instantiated according to the data in the given context object
     * and the array.
     *
     * @param RelationshipContext $context
     * @param array $data
     * @return RelationshipContext
     */
    public static function fromExistingAndData(
        RelationshipContext $context,
        array $data
    ): RelationshipContext {

        $instance = new static();
        $instance->data = combineAtts($context->data, $data);

        if (
            !array_key_exists(self::SOURCE_POST_ID, $data)
            && array_key_exists('source', $context->posts)
        ) {
            $instance->posts['source'] = $context->posts['source'];
        }

        if (
            !array_key_exists(self::REMOTE_POST_ID, $data)
            && array_key_exists('remote', $context->posts)
        ) {
            $instance->posts['remote'] = $context->posts['remote'];
        }

        return $instance;
    }

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if (!is_array($this->data)) {
            $this->data = combineAtts(self::DEFAULTS, $data);
        }
    }

    /**
     * Returns the remote post ID.
     *
     * @return int
     */
    public function remotePostId(): int
    {
        return (int)$this->data[static::REMOTE_POST_ID];
    }

    /**
     * Returns the remote site ID.
     *
     * @return int
     */
    public function remoteSiteId(): int
    {
        return (int)$this->data[static::REMOTE_SITE_ID];
    }

    /**
     * Returns the source post ID.
     *
     * @return int
     */
    public function sourcePostId(): int
    {
        return (int)$this->data[static::SOURCE_POST_ID];
    }

    /**
     * Returns the source site ID.
     *
     * @return int
     */
    public function sourceSiteId(): int
    {
        return (int)$this->data[static::SOURCE_SITE_ID];
    }

    /**
     * Returns the source post object.
     *
     * @return bool
     */
    public function hasRemotePost(): bool
    {
        return $this->remotePost() instanceof \WP_Post;
    }

    /**
     * Returns the source post object.
     *
     * @return \WP_Post|null
     */
    public function remotePost()
    {
        return $this->post($this->remoteSiteId(), 'remote');
    }

    /**
     * Returns the source post object.
     *
     * @return \WP_Post
     */
    public function sourcePost(): \WP_Post
    {
        return $this->post($this->sourceSiteId(), 'source') ?: new \WP_Post(new \stdClass());
    }

    /**
     * Print HTML fields for the relationship context.
     * @param MetaboxFieldsHelper $helper
     */
    public function renderFields(MetaboxFieldsHelper $helper)
    {
        $baseName = $helper->fieldName('relation_context');
        $baseId = $helper->fieldId('relation_context');

        $fields = [
            RelationshipContext::SOURCE_SITE_ID => [$this, 'sourceSiteId'],
            RelationshipContext::SOURCE_POST_ID => [$this, 'sourcePostId'],
            RelationshipContext::REMOTE_SITE_ID => [$this, 'remoteSiteId'],
            RelationshipContext::REMOTE_POST_ID => [$this, 'remotePostId'],
        ];

        foreach ($fields as $key => $callback) {
            ?>
            <input
                type="hidden"
                class="relationship-context-fields"
                name="<?= esc_attr("{$baseName}[{$key}]") ?>"
                id="<?= esc_attr("{$baseId}-{$key}") ?>"
                value="<?= esc_attr((string)$callback()) ?>">
            <?php
        }
    }

    /**
     * Returns the source post object.
     *
     * @param int $siteId
     * @param string $type
     * @return \WP_Post|null
     */
    private function post(int $siteId, string $type)
    {
        if (!array_key_exists($type, $this->posts)) {
            if (!$siteId) {
                $this->posts[$type] = null;
                return null;
            }

            $postId = $this->{"{$type}PostId"}();
            if (!$postId) {
                $this->posts[$type] = null;
                return null;
            }

            switch_to_blog($siteId);
            $this->posts[$type] = get_post($postId);
            restore_current_blog();
        }

        return $this->posts[$type] ?: null;
    }
}
