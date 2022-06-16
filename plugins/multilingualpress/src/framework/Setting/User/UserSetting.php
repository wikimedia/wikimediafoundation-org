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

namespace Inpsyde\MultilingualPress\Framework\Setting\User;

use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * User setting.
 */
class UserSetting
{
    /**
     * @var bool
     */
    private $checkUser;

    /**
     * @var UserSettingViewModel
     */
    private $model;

    /**
     * @var UserSettingUpdater
     */
    private $updater;

    /**
     * @param UserSettingViewModel $model
     * @param UserSettingUpdater $updater
     * @param bool $checkUser
     */
    public function __construct(
        UserSettingViewModel $model,
        UserSettingUpdater $updater,
        bool $checkUser = true
    ) {

        $this->model = $model;
        $this->updater = $updater;
        $this->checkUser = $checkUser;
    }

    /**
     * Registers the according callbacks.
     */
    public function register()
    {
        add_action(
            'personal_options',
            function (\WP_User $user) {
                $view = new UserSettingView($this->model, $this->checkUser);
                $view->render($user);
            }
        );

        add_action('profile_update', wpHookProxy(function (int $userId) {
            $this->updater->update($userId);
        }));
    }
}
