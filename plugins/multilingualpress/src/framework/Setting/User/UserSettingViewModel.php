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

namespace Inpsyde\MultilingualPress\Framework\Setting\User;

/**
 * Interface for all user setting view model implementations.
 */
interface UserSettingViewModel
{

    /**
     * Renders the user setting.
     *
     * @param \WP_User $user
     */
    public function render(\WP_User $user);

    /**
     * Returns the title of the user setting.
     *
     * @return string
     */
    public function title(): string;
}
