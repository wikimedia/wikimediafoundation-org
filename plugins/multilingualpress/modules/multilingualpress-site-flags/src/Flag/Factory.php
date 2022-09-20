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

class Factory
{
    /**
     * @var SiteSettingsRepository
     */
    private $settingsRepository;

    /**
     * @var string
     */
    protected $pathToFlagsFolder;

    /**
     * @var string
     */
    protected $flagImageExtension;

    /**
     * @var string
     */
    protected $pluginPath;

    /**
     * @var string
     */
    protected $pluginUrl;

    public function __construct(
        SiteSettingsRepository $settingsRepository,
        string $pathToFlagsFolder,
        string $flagImageExtension,
        string $pluginPath,
        string $pluginUrl
    ) {

        $this->settingsRepository = $settingsRepository;
        $this->pathToFlagsFolder = $pathToFlagsFolder;
        $this->flagImageExtension = $flagImageExtension;
        $this->pluginPath = $pluginPath;
        $this->pluginUrl = $pluginUrl;
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
     * Will return the flag url of a given language.
     *
     * @param Language $language
     * @return string The flag url.
     */
    protected function flag(Language $language): string
    {
        $languageCode = $language->isoCode();
        $bcp47tag = $language->bcp47tag();

        $flagPath = "{$this->pluginPath}{$this->pathToFlagsFolder}{$bcp47tag}{$this->flagImageExtension}";
        $flagImageName = file_exists($flagPath) ? $bcp47tag : $languageCode;

        return "{$this->pluginUrl}{$this->pathToFlagsFolder}{$flagImageName}{$this->flagImageExtension}";
    }
}
