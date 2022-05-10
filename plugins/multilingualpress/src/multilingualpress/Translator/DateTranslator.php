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

namespace Inpsyde\MultilingualPress\Translator;

use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\Translator\Translator;

/**
 * Class DateTranslator
 * @package Inpsyde\MultilingualPress\Translator
 */
final class DateTranslator implements Translator
{
    use UrlBlogFragmentTrailingTrait;

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var \WP
     */
    private $wp = null;

    /**
     * @var \WP_Rewrite
     */
    private $wpRewrite = null;

    /**
     * DateTranslator constructor.
     * @param UrlFactory $urlFactory
     */
    public function __construct(UrlFactory $urlFactory)
    {
        $this->urlFactory = $urlFactory;
    }

    /**
     * @param int $siteId
     * @param TranslationSearchArgs $args
     * @return Translation
     */
    public function translationFor(int $siteId, TranslationSearchArgs $args): Translation
    {
        $translation = new Translation();

        if (!$this->ensureWp() || !$this->ensureWpRewrite()) {
            return $translation;
        }

        switch_to_blog($siteId);
        $remotePermalinkStructure = (string)get_option('permalink_structure');
        restore_current_blog();

        $hasBlogPrefix = false !== strpos($remotePermalinkStructure, '/blog/');

        if (!$this->ensurePermalinkStructure($remotePermalinkStructure)) {
            return $translation;
        }

        $request = $this->ensureRequestFragment($hasBlogPrefix, $siteId);

        $remoteUrl = get_home_url($siteId, $request);
        $remoteUrl and $remoteUrl = $this->urlFactory->create([$remoteUrl]);
        $translation = $translation->withRemoteUrl($remoteUrl);

        return $translation;
    }

    /**
     * @param \WP|null $wp
     * @return bool
     */
    public function ensureWp(\WP $wp = null): bool
    {
        if ($this->wp && !$wp) {
            return true;
        }

        if (!$wp && empty($GLOBALS['wp'])) {
            return false;
        }

        $this->wp = $wp ?: $GLOBALS['wp'];

        return true;
    }

    /**
     * @param \WP_Rewrite|null $wp_rewrite
     * @return bool
     */
    public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null): bool
    {
        if ($this->wpRewrite && !$wp_rewrite) {
            return true;
        }

        if (!$wp_rewrite && empty($GLOBALS['wp_rewrite'])) {
            return false;
        }

        $this->wpRewrite = $wp_rewrite ?: $GLOBALS['wp_rewrite'];

        return true;
    }

    /**
     * @param string $struct
     * @return bool
     */
    private function ensurePermalinkStructure(string $struct): bool
    {
        $permalinkStructure = $this->untrailingBlogIt($this->wpRewrite->permalink_structure);
        $struct = $this->untrailingBlogIt($struct);

        return $struct === $permalinkStructure;
    }

    /**
     * @param bool $hasBlogPrefix
     * @param int $siteId
     * @return string
     */
    private function ensureRequestFragment(bool $hasBlogPrefix, int $siteId): string
    {
        $request = $this->untrailingBlogIt($this->wp->request);

        if ($hasBlogPrefix) {
            $request = $this->trailingBlogIt($request);
        }

        $request = '/' . ltrim($request, '/');

        return $request;
    }
}
