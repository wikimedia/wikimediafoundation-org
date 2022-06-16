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

namespace Inpsyde\MultilingualPress\Framework\Nonce;

/**
 * WordPress-specific nonce implementation.
 */
final class WpNonce implements SiteAwareNonce
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $siteId;

    /**
     * @param string $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * @inheritdoc
     */
    public function withSite(int $siteId): SiteAwareNonce
    {
        $clone = clone $this;
        $clone->siteId = $siteId;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return (string)wp_create_nonce($this->actionHash());
    }

    /**
     * @inheritdoc
     */
    public function action(): string
    {
        return $this->action;
    }

    /**
     * @inheritdoc
     */
    public function isValid(Context $context = null): bool
    {
        $context or $context = new ServerRequestContext();

        if (empty($context[$this->action])) {
            return false;
        }

        $nonce = $context[$this->action];

        return is_string($nonce) && wp_verify_nonce($nonce, $this->actionHash());
    }

    /**
     * Returns a hash for the current action and site ID.
     *
     * @return string
     */
    private function actionHash(): string
    {
        $siteId = $this->siteId ?? get_current_blog_id();

        return sha1($this->action . $siteId);
    }
}
