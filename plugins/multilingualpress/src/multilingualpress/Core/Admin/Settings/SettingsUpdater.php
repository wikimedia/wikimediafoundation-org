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

namespace Inpsyde\MultilingualPress\Core\Admin\Settings;

use Inpsyde\MultilingualPress\Framework\Http\Request;

/**
 * Interface SettingsUpdater
 * @package Inpsyde\MultilingualPress\Core\Admin\Settings
 */
interface SettingsUpdater
{
    /**
     * Update Settings
     *
     * @param Request $request
     * @return bool
     */
    public function updateSettings(Request $request): bool;
}
