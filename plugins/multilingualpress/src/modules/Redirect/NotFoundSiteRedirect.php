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

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository;

use function Inpsyde\MultilingualPress\callExit;
use function Inpsyde\MultilingualPress\isWpDebugMode;
use function Inpsyde\MultilingualPress\siteLanguageTag;
use function Inpsyde\MultilingualPress\siteLocale;

/**
 * Class NotFoundSiteRedirect
 * @package Inpsyde\MultilingualPress\Module\Redirect
 */
class NotFoundSiteRedirect implements Redirector
{
    /**
     * @var Repository
     */
    private $redirectSettingsRepository;

    /**
     * @var NoRedirectStorage
     */
    private $noRedirectStorage;

    /**
     * NotFoundSiteRedirect constructor.
     * @param Repository $redirectSettingsRepository
     */
    public function __construct(
        Repository $redirectSettingsRepository,
        NoRedirectStorage $noRedirectStorage
    ) {

        $this->redirectSettingsRepository = $redirectSettingsRepository;
        $this->noRedirectStorage = $noRedirectStorage;
    }

    /**
     * @inheritDoc
     */
    public function redirect(): bool
    {
        $siteIdToRedirectTo = $this->redirectSettingsRepository->redirectFallbackSiteId();

        if (!$siteIdToRedirectTo || $siteIdToRedirectTo < 1) {
            return false;
        }

        try {
            $siteUrlToRedirectTo = $this->redirectUrlForSite($siteIdToRedirectTo);
        } catch (NonexistentTable $exc) {
            if (isWpDebugMode()) {
                throw new $exc();
            }

            return false;
        }

        if (!$siteUrlToRedirectTo) {
            return false;
        }

        $siteLanguageTag = siteLanguageTag($siteIdToRedirectTo);
        $this->noRedirectStorage->addLanguage($siteLanguageTag);

        $this->redirectToUrl($siteUrlToRedirectTo);

        return false;
    }

    /**
     * Retrieve the Site Url Where Redirect the User
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    protected function redirectUrlForSite(int $siteId): string
    {
        $siteLocale = siteLocale($siteId);

        $siteUrlToRedirectTo = get_site_url($siteId);

        return add_query_arg(
            [
                'noredirect' => $siteLocale,
            ],
            $siteUrlToRedirectTo
        );
    }

    /**
     * Do the Redirect and Stop the Execution
     *
     * @param string $url
     */
    protected function redirectToUrl(string $url)
    {
        //phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
        wp_redirect($url) and callExit();
    }
}
