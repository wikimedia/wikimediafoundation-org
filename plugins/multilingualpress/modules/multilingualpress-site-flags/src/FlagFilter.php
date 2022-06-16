<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress Site Flag package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Inpsyde\MultilingualPress\SiteFlags;

use Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteMenuLanguageStyleSetting;
use Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\SiteFlags\Flag\Factory;
use Inpsyde\MultilingualPress\NavMenu\ItemRepository;

/**
 * Class FlagFilter
 */
class FlagFilter
{
    /**
     * @var SiteSettingsRepository
     */
    private $settingsRepository;

    /**
     * @var Factory
     */
    private $flagFactory;

    /**
     * @var string
     */
    private $flagsPath;

    /**
     * NavMenuLanguageStyleFilter constructor
     * @param SiteSettingsRepository $settingsRepository
     * @param Factory $flagFactory
     */
    public function __construct(SiteSettingsRepository $settingsRepository, Factory $flagFactory, string $flagsPath)
    {
        $this->settingsRepository = $settingsRepository;
        $this->flagFactory = $flagFactory;
        $this->flagsPath = $flagsPath;
    }

    /**
     * Show the flags on nav menu items based on site settings
     *
     * @param string $title
     * @param \WP_Post $item
     * @return string
     */
    public function navMenuItems(string $title, \WP_Post $item): string
    {
        if ($item->object !== ItemRepository::ITEM_TYPE) {
            return $title;
        }

        $siteId = (int)get_post_meta($item->ID, ItemRepository::META_KEY_SITE_ID, true);
        $menuStyle = $this->settingsRepository->siteMenuLanguageStyle(get_current_blog_id());
        $flag = $this->flagFactory->create($siteId);
        $menuUseFlags = [
            SiteMenuLanguageStyleSetting::FLAG_AND_LANGUAGES,
            SiteMenuLanguageStyleSetting::ONLY_FLAGS,
        ];

        if ($menuStyle === SiteMenuLanguageStyleSetting::ONLY_FLAGS) {
            $title = "<span class=\"screen-reader-text\">{$title}</span>";
        }
        if (\in_array($menuStyle, $menuUseFlags, true)) {
            $title = $flag->markup() . ' ' . $title;
        }

        return $title;
    }

    /**
     * Show the flags on language switcher items
     *
     * @param string $flag
     * @param string $isoCode
     * @return string
     */
    public function languageSwitcherItems(string $flag, string $isoCode): string
    {
        return "{$this->flagsPath}/{$isoCode}.gif";
    }

    /**
     * Show flags in the table list columns for translated content
     *
     * @param string $languageTag
     * @param int $siteId
     * @return string
     */
    public function tableListPostsRelations(string $languageTag, int $siteId): string
    {
        $flag = $this->flagFactory->create($siteId);

        return sprintf(
            '%1$s <span class="screen-reader-text">%2$s</span>',
            $flag->markup(),
            $languageTag
        );
    }
}
