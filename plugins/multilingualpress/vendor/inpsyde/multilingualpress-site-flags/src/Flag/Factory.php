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

namespace Inpsyde\MultilingualPress\SiteFlags\Flag;

use Inpsyde\MultilingualPress\Framework\Language\Language;
use Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository;

use function Inpsyde\MultilingualPress\siteLanguageTag;
use function Inpsyde\MultilingualPress\languageByTag;

/**
 * MultilingualPress Flag Factory
 */
class Factory
{
    /**
     * @var SiteSettingsRepository
     */
    private $settingsRepository;

    /**
     * Factory constructor
     * @param SiteSettingsRepository $settingsRepository
     */
    public function __construct(SiteSettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param int $siteId
     * @return Flag
     */
    public function create(int $siteId): Flag
    {
        $language = languageByTag(siteLanguageTag($siteId));
        $url = $this->flagUrlBySetting($siteId);

        return new Raster($language, $url ?: $this->flag($language));
    }

    /**
     * @param int $siteId
     * @return string
     */
    private function flagUrlBySetting(int $siteId): string
    {
        return $this->settingsRepository->siteFlagUrl($siteId);
    }

    /**
     * @param Language $language
     * @return string
     */
    private function flag(Language $language): string
    {
        $languageCode = $language->isoCode();
        $siteFlagUrl = plugin_dir_url(dirname(__DIR__))
            . "resources/images/flags/{$languageCode}.gif";

        return $siteFlagUrl;
    }
}
