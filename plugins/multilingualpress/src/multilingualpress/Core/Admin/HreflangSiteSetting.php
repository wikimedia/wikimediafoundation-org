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

use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

/**
 * Hreflang site setting.
 */
final class HreflangSiteSetting implements SiteSettingViewModel
{
    /**
     * @var array<SettingOptionInterface>
     */
    private $options;

    /**
     * @var SiteSettingsRepository
     */
    private $siteSettingsRepository;

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * Hreflang site setting constructor.
     *
     * @param array<SettingOptionInterface> $options
     * @param SiteRelations $siteRelations
     * @param SiteSettingsRepository $siteSettingsRepository
     */
    public function __construct(
        array $options,
        SiteRelations $siteRelations,
        SiteSettingsRepository $siteSettingsRepository
    ) {

        $this->options = $options;
        $this->siteRelations = $siteRelations;
        $this->siteSettingsRepository = $siteSettingsRepository;
    }

    /**
     * @inheritdoc
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    public function render(int $siteId)
    {
        // phpcs:enable

        foreach ($this->options as $option) {
            ?>
            <div class="<?= esc_attr($option->id()) ?>">
                <label for="<?= esc_attr($option->id()) ?>">
                    <p class="label"><strong><?= esc_html($option->label()) ?></strong></p>
                    <p class="input">
                    <?php
                    switch ($option->id()) {
                        case SiteSettingsRepository::NAME_HREFLANG_DISPLAY_TYPE:
                            $this->renderDisplayType($siteId, $option);
                            break;
                        default:
                            $this->renderXDefault($siteId, $option);
                    }
                    ?>
                    </p>
                </label>
                <p class="description"><?= esc_html($option->description()) ?></p>
            </div>
            <?php
        }
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="hreflang">%1$s</label>',
            esc_html__('Hreflang', 'multilingualpress')
        );
    }

    /**
     * Render the xDefault selectbox
     *
     * @param int $siteId
     * @param SettingOptionInterface $option
     */
    protected function renderXDefault(int $siteId, SettingOptionInterface $option): void
    {
        $relatedSites = $this->relatedSites($siteId);
        $xDefault = $this->siteSettingsRepository->hreflangSettingForSite($siteId, $option->value());
        ?>
        <select id="<?= esc_attr($option->id()) ?>" name="<?= esc_attr($this->settingOptionName($option->id())) ?>">
            <option value="0"><?= esc_html__('None', 'multilingualpress'); ?></option>
            <?php
            foreach ($relatedSites as $site) {
                $currentSiteId = (int)$site->blog_id;
                $url = untrailingslashit($site->domain) . trailingslashit($site->path);
                ?>
                <option
                    <?= selected($currentSiteId, $xDefault, false)?>
                    value="<?= esc_attr($currentSiteId);?>">
                    <?= esc_html($url); ?>
                </option>
                <?php
            }
            ?>
        </select>
        <?php
    }

    /**
     * Render the Display Type radio buttons
     *
     * @param int $siteId
     * @param SettingOptionInterface $option
     */
    protected function renderDisplayType(int $siteId, SettingOptionInterface $option): void
    {
        $displayStyles = [
            'country_region' => __('Country & Region', 'multilingualpress'),
            'country' => __('Country', 'multilingualpress'),
        ];
        $displayType = $this->siteSettingsRepository->hreflangSettingForSite($siteId, $option->value());
        $displayType = !empty($displayType) ? $displayType : 'country_region';

        foreach ($displayStyles as $displayStyleKey => $displayStyleValue) {
            ?>
            <input
                type="radio"
                id="<?= esc_attr($displayStyleKey);?>"
                name="<?= esc_attr($this->settingOptionName($option->id())) ?>"
                value="<?= esc_attr($displayStyleKey);?>"
                <?= checked($displayStyleKey, $displayType, false)?>>
            <label for="html"><?= esc_html($displayStyleValue);?></label>
            <?php
        }
    }

    /**
     * Retrieve all the related sites according to the given parameter
     *
     * @param int $siteId
     * @return array
     */
    private function relatedSites(int $siteId): array
    {
        $sites = $this->siteRelations->relatedSiteIds($siteId, true);

        foreach ($sites as &$site) {
            $site = get_site($site);
        }

        return $sites;
    }

    /**
     * Create the setting option name
     *
     * @param string $optionName The option name
     * @return string The setting option name
     */
    protected function settingOptionName(string $optionName): string
    {
        $settingName = SiteSettingsRepository::NAME_HREFLANG;
        return "{$settingName}[{$optionName}]";
    }
}
