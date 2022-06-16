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

use Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository;

use function Inpsyde\MultilingualPress\siteLanguageTag;

/**
 * Request validator to be used for (potential) redirect requests.
 */
class RedirectRequestChecker
{
    const FILTER_REDIRECT = 'multilingualpress.do_redirect';

    /**
     * @var NoRedirectStorage
     */
    private $noRedirectStorage;

    /**
     * @var Repository
     */
    private $settingsRepository;

    /**
     * @param Repository $settingsRepository
     * @param NoRedirectStorage $redirectStorage
     */
    public function __construct(
        Repository $settingsRepository,
        NoRedirectStorage $redirectStorage
    ) {

        $this->settingsRepository = $settingsRepository;
        $this->noRedirectStorage = $redirectStorage;
    }

    /**
     * @return bool
     */
    public function isRedirectRequest(): bool
    {
        if ('wp-login.php' === ($GLOBALS['pagenow'] ?? '')) {
            return false;
        }

        if (
            !$this->settingsRepository->isRedirectSettingEnabledForSite()
            || $this->settingsRepository->isRedirectEnabledForUser()
            || $this->noRedirectStorage->hasLanguage(siteLanguageTag())
        ) {
            return false;
        }

        /**
         * Filters if the current request should be redirected.
         *
         * @param bool $redirect
         */
        return (bool)apply_filters(static::FILTER_REDIRECT, true);
    }
}
