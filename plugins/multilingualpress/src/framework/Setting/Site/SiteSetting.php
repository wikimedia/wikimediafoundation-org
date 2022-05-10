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
 * Site setting.
 */
class SiteSetting
{

    /**
     * @var bool
     */
    private $checkUser;

    /**
     * @var SiteSettingViewModel
     */
    private $model;

    /**
     * @var SiteSettingUpdater
     */
    private $updater;

    /**
     * @param SiteSettingViewModel $model
     * @param SiteSettingUpdater $updater
     * @param bool $checkUser
     */
    public function __construct(
        SiteSettingViewModel $model,
        SiteSettingUpdater $updater,
        bool $checkUser = true
    ) {

        $this->model = $model;
        $this->updater = $updater;
        $this->checkUser = $checkUser;
    }

    /**
     * Registers the according callbacks.
     *
     * @param string $renderHook
     * @param string $updateHook
     */
    public function register(string $renderHook, string $updateHook = '')
    {
        add_action(
            $renderHook,
            function (int $siteId) {
                (new SiteSettingSingleView($this->model, $this->checkUser))
                    ->render($siteId);
            }
        );

        if ($updateHook) {
            add_action($updateHook, [$this->updater, 'update']);
        }
    }
}
