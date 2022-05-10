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

namespace Inpsyde\MultilingualPress\Framework\Admin;

/**
 * Interface for all settings page view implementations.
 */
interface SettingsPageView
{

    /**
     * Renders the markup.
     */
    public function render();
}
