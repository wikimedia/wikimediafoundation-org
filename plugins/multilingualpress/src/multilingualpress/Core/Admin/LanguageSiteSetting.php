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

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

use function Inpsyde\MultilingualPress\allLanguages;
use function Inpsyde\MultilingualPress\siteLanguageTag;

/**
 * MultilingualPress "Language" site setting.
 */
final class LanguageSiteSetting implements SiteSettingViewModel
{
    /**
     * @var string
     */
    private $id = 'mlp-site-language';

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        ?>
        <select
            id="<?= esc_attr($this->id) ?>"
            name="<?= esc_attr(SiteSettingsRepository::NAME_LANGUAGE) ?>"
            autocomplete="off">
            <?php $this->renderOptions($siteId) ?>
        </select>
        <?php
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('MultilingualPress Language', 'multilingualpress'),
            esc_attr($this->id)
        );
    }

    /**
     * Renders the option tags.
     *
     * @param int $siteId
     */
    private function renderOptions(int $siteId)
    {
        $currentSiteLanguage = $this->currentSiteLanguage($siteId);
        ?>
        <option value="">
            <?php esc_html_e('Choose language', 'multilingualpress') ?>
        </option>
        <?php
        $languages = allLanguages();
        if (!$languages) {
            return;
        }

        foreach ($languages as $language) {
            $siteLanguage = $language->bcp47tag();
            $iso = $language->isoCode(LanguagesTable::COLUMN_ISO_639_1_CODE);
            ?>
            <option
                value="<?= esc_attr($siteLanguage) ?>"
                data-locale="<?= esc_attr($language->locale()) ?>"
                data-iso="<?= esc_attr($iso) ?>"
                <?php selected($siteLanguage, $currentSiteLanguage) ?>>
                <?= esc_html($language->name()) ?>
            </option>
            <?php
        }
    }

    /**
     * Returns the current MultilingualPress or WordPress language for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     */
    private function currentSiteLanguage(int $siteId): string
    {
        if (!$siteId) {
            return 'en-US';
        }

        $siteLanguage = siteLanguageTag($siteId);

        if (!$siteLanguage) {
            // For English (US), WordPress stores an empty string.
            $siteLanguage = (string)get_blog_option($siteId, 'WPLANG', '') ?: 'en_US';
            $siteLanguage = str_replace('_', '-', $siteLanguage);
        }

        return $siteLanguage;
    }
}
