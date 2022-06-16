<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the Inpsyde Unprefix Theme package.
 *
 * (c) Guido Scialfa <dev@guidoscialfa.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\SiteFlags\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable;

/**
 * Class SiteSettingsUpdater
 */
final class SiteSettingsUpdater implements SiteSettingsUpdatable
{
    /**
     * @var SiteSettingsRepository
     */
    private $repository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param SiteSettingsRepository $repository
     * @param Request $request
     */
    public function __construct(SiteSettingsRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function defineInitialSettings(int $siteId)
    {
        $this->updateSettings($siteId);
    }

    /**
     * @inheritdoc
     */
    public function updateSettings(int $siteId)
    {
        $this->updateSiteFlagUrl($siteId);
        $this->updateSiteMenuLanguageStyle($siteId);
    }

    /**
     * @param int $siteId
     */
    private function updateSiteFlagUrl(int $siteId)
    {
        $siteFlagUrl = $this->request->bodyValue(
            SiteSettingsRepository::KEY_SITE_FLAG_URL,
            INPUT_POST,
            FILTER_SANITIZE_URL
        );
        if (!is_string($siteFlagUrl)) {
            $siteFlagUrl = '';
        }

        $this->repository->updateSiteFlagUrl(
            $siteFlagUrl,
            $siteId
        );
    }

    /**
     * @param int $siteId
     */
    private function updateSiteMenuLanguageStyle(int $siteId)
    {
        $siteMenuLanguageStyle = $this->request->bodyValue(
            SiteSettingsRepository::KEY_SITE_MENU_LANGUAGE_STYLE,
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );
        if (!is_string($siteMenuLanguageStyle)) {
            $siteMenuLanguageStyle = '';
        }

        $this->repository->updateMenuLanguageStyle(
            $siteMenuLanguageStyle,
            $siteId
        );
    }
}
