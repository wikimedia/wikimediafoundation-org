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

namespace Inpsyde\MultilingualPress\Framework\Api;

use Inpsyde\MultilingualPress\Framework\WordpressContext;

class TranslationSearchArgs
{
    const CONTENT_ID = 'content_id';
    const INCLUDE_BASE = 'include_base';
    const POST_STATUS = 'post_status';
    const POST_TYPE = 'post_type';
    const SEARCH_TERM = 'search_term';
    const SITE_ID = 'site_id';
    const STRICT = 'strict';
    const TYPE = 'type';

    const KEYS = [
        self::CONTENT_ID => 'is_int',
        self::INCLUDE_BASE => null,
        self::POST_STATUS => 'is_array',
        self::POST_TYPE => 'is_string',
        self::SEARCH_TERM => 'is_string',
        self::SITE_ID => 'is_int',
        self::STRICT => null,
        self::TYPE => 'is_string',
    ];

    /**
     * @var array
     */
    private $data;

    /**
     * @param WordpressContext $context
     * @param array $data
     * @return static
     */
    public static function forContext(
        WordpressContext $context,
        array $data = []
    ): TranslationSearchArgs {

        $instance = new static($data);
        $instance = $instance->forType($context->type());

        $queriedId = $context->queriedObjectId();
        $postType = $context->postType();

        $queriedId and $args = $instance->forContentId($queriedId);
        $postType and $args = $instance->forPostType($postType);
        if ($context->isType(WordpressContext::TYPE_SEARCH)) {
            $instance = $instance->searchFor(get_search_query());
        }

        return $instance;
    }

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data ? array_intersect_key(array_filter($data), self::KEYS) : [];
    }

    /**
     * @return int|null
     */
    public function contentId()
    {
        return $this->property(self::CONTENT_ID);
    }

    /**
     * @param int $contentId
     * @return static
     */
    public function forContentId(int $contentId): TranslationSearchArgs
    {
        $this->data[self::CONTENT_ID] = $contentId;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldIncludeBase(): bool
    {
        return $this->property(self::INCLUDE_BASE);
    }

    /**
     * @return static
     */
    public function includeBase(): TranslationSearchArgs
    {
        $this->data[self::INCLUDE_BASE] = true;

        return $this;
    }

    /**
     * @return static
     */
    public function dontIncludeBase(): TranslationSearchArgs
    {
        $this->data[self::INCLUDE_BASE] = false;

        return $this;
    }

    /**
     * @return array
     */
    public function postStatus(): array
    {
        return $this->property(self::POST_STATUS) ?: [];
    }

    /**
     * @param string[] $postStatus
     * @return static
     */
    public function forPostStatus(string ...$postStatus): TranslationSearchArgs
    {
        $this->data[self::POST_STATUS] = $postStatus;

        return $this;
    }

    /**
     * @return null|string
     */
    public function postType()
    {
        return $this->property(self::POST_TYPE);
    }

    /**
     * @param string $postType
     * @return static
     */
    public function forPostType(string $postType): TranslationSearchArgs
    {
        $this->data[self::POST_TYPE] = $postType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function searchTerm()
    {
        return $this->property(self::SEARCH_TERM);
    }

    /**
     * @param string $searchTerm
     * @return static
     */
    public function searchFor(string $searchTerm): TranslationSearchArgs
    {
        $this->data[self::SEARCH_TERM] = $searchTerm;

        return $this;
    }

    /**
     * @return int|null
     */
    public function siteId()
    {
        return $this->property(self::SITE_ID);
    }

    /**
     * @param int $siteId
     * @return static
     */
    public function forSiteId(int $siteId): TranslationSearchArgs
    {
        $this->data[self::SITE_ID] = $siteId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStrict(): bool
    {
        return $this->property(self::STRICT);
    }

    /**
     * @return static
     */
    public function makeStrictSearch(): TranslationSearchArgs
    {
        $this->data[self::STRICT] = true;

        return $this;
    }

    /**
     * @return static
     */
    public function makeNotStrictSearch(): TranslationSearchArgs
    {
        $this->data[self::STRICT] = false;

        return $this;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->property(self::TYPE) ?: '';
    }

    /**
     * @param string $type
     * @return static
     */
    public function forType(string $type): TranslationSearchArgs
    {
        $this->data[self::TYPE] = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        $keys = array_keys($this->data);

        foreach ($keys as $key) {
            $data[$key] = $this->property($key);
        }

        return $data;
    }

    /**
     * @param string $key
     * @return string|int|bool|array|null
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    private function property(string $key)
    {
        // phpcs:enable

        $value = $this->data[$key] ?? null;
        $check = self::KEYS[$key] ?? null;
        if ($check && !$check($value)) {
            $value = null;
        }

        return $check === null ? (bool)$value : $value;
    }
}
