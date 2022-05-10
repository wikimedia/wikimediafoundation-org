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

namespace Inpsyde\MultilingualPress\Framework\Setting\Site;

/**
 * Site setting view implementation for multiple single settings.
 */
final class SiteSettingMultiView implements SiteSettingView
{
    /**
     * @var bool
     */
    private $checkUser;

    /**
     * @var SiteSettingView[]
     */
    private $views;

    /**
     * Returns a new instance.
     *
     * @param SiteSettingViewModel[] $settings
     * @param bool $checkUser
     * @return SiteSettingMultiView
     */
    public static function fromViewModels(
        array $settings,
        bool $checkUser = true
    ): SiteSettingMultiView {

        $settings = array_filter(
            $settings,
            static function (SiteSettingViewModel $setting): bool {
                return (bool) $setting;
            }
        );

        $views = array_map(
            static function (SiteSettingViewModel $setting): SiteSettingSingleView {
                return new SiteSettingSingleView($setting, false);
            },
            $settings
        );

        return new static($views, $checkUser);
    }

    /**
     * @param SiteSettingView[] $views
     * @param bool $checkUser
     */
    public function __construct(array $views, bool $checkUser = true)
    {
        $this->views = array_filter(
            $views,
            static function (SiteSettingView $view): bool {
                return (bool) $view;
            }
        );

        $this->checkUser = $checkUser;
    }

    /**
     * @inheritdoc
     *
     * @wp-hook SiteSettingsSectionView::ACTION_AFTER . '_' . NewSiteSettings::SECTION_ID
     */
    public function render(int $siteId): bool
    {
        if ($this->checkUser && !current_user_can('manage_sites')) {
            return false;
        }

        if (!$this->views) {
            return false;
        }

        array_walk(
            $this->views,
            static function (SiteSettingView $view) use ($siteId) {
                $view->render($siteId);
            }
        );

        return true;
    }
}
