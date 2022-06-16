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

use Inpsyde\MultilingualPress\Framework\Language\Language;
use Inpsyde\MultilingualPress\Framework\Language\NullLanguage;
use Inpsyde\MultilingualPress\Framework\Url\Url;

class Translation
{
    const FILTER_URL = 'multilingualpress.translation_url';

    const REMOTE_TITLE = 'remote_title';
    const REMOTE_URL = 'remote_url';
    const REMOTE_CONTENT_ID = 'target_content_id';
    const REMOTE_SITE_ID = 'target_site_id';
    const SOURCE_SITE_ID = 'source_site_id';
    const TYPE = 'type';

    const KEYS = [
        self::REMOTE_TITLE => 'is_string',
        self::REMOTE_URL => 'is_string',
        self::REMOTE_CONTENT_ID => 'is_int',
        self::REMOTE_SITE_ID => 'is_int',
        self::SOURCE_SITE_ID => 'is_int',
        self::TYPE => 'is_string',
    ];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var Language|null
     */
    private $language;

    /**
     * @param Language|null $language
     */
    public function __construct(Language $language = null)
    {
        $this->language = $language;
    }

    /**
     * @param Translation $translation
     * @return Translation
     */
    public function merge(Translation $translation): Translation
    {
        $this->data = array_filter(array_merge($translation->data, $this->data));
        if (!$this->language && $translation->language) {
            $this->language = $translation->language;
        }

        return $this;
    }

    /**
     * @return Language
     */
    public function language(): Language
    {
        return $this->language ?: new NullLanguage();
    }

    /**
     * @return string
     */
    public function remoteTitle(): string
    {
        return $this->property(self::REMOTE_TITLE) ?: '';
    }

    /**
     * @param string $title
     * @return Translation
     */
    public function withRemoteTitle(string $title): Translation
    {
        $this->data[self::REMOTE_TITLE] = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function remoteUrl(): string
    {
        /**
         * Filters the URL of the remote element.
         *
         * @param string $remoteUrl
         * @param Translation $translation
         */
        $remoteUrl = apply_filters(
            self::FILTER_URL,
            (string)($this->data[self::REMOTE_URL] ?? ''),
            $this->remoteSiteId(),
            $this->remoteContentId(),
            $this
        );

        return (string)($remoteUrl ?: '');
    }

    /**
     * @param Url $url
     * @return Translation
     */
    public function withRemoteUrl(Url $url): Translation
    {
        $this->data[self::REMOTE_URL] = $url;

        return $this;
    }

    /**
     * @return int
     */
    public function remoteContentId(): int
    {
        return $this->property(self::REMOTE_CONTENT_ID) ?: 0;
    }

    /**
     * @param int $contentId
     * @return Translation
     */
    public function withRemoteContentId(int $contentId): Translation
    {
        $this->data[self::REMOTE_CONTENT_ID] = $contentId;

        return $this;
    }

    /**
     * @return int
     */
    public function remoteSiteId(): int
    {
        return $this->property(self::REMOTE_SITE_ID) ?: 0;
    }

    /**
     * @param int $siteId
     * @return Translation
     */
    public function withRemoteSiteId(int $siteId): Translation
    {
        $this->data[self::REMOTE_SITE_ID] = $siteId;

        return $this;
    }

    /**
     * @return int
     */
    public function sourceSiteId(): int
    {
        return $this->property(self::SOURCE_SITE_ID) ?: 0;
    }

    /**
     * @param int $siteId
     * @return Translation
     */
    public function withSourceSiteId(int $siteId): Translation
    {
        $this->data[self::SOURCE_SITE_ID] = $siteId;

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
     * @return Translation
     */
    public function withType(string $type): Translation
    {
        $this->data[self::TYPE] = $type;

        return $this;
    }

    /**
     * @param string $key
     * @return string|int|bool|null
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
