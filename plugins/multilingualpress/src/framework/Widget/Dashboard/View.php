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

namespace Inpsyde\MultilingualPress\Framework\Widget\Dashboard;

/**
 * Interface for all dashboard widget view implementations.
 */
interface View
{
    /**
     * Renders the widget's view.
     *
     * @param array $widgetInstanceSettings
     */
    public function render(array $widgetInstanceSettings);
}
