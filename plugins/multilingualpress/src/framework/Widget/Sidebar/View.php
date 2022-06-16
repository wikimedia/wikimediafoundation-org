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

namespace Inpsyde\MultilingualPress\Framework\Widget\Sidebar;

/**
 * Interface for all sidebar widget view implementations.
 */
interface View
{
    /**
     * Renders the widget's front end view.
     *
     * @param array $args
     * @param array $instance
     * @param string $idBase
     */
    public function render(array $args, array $instance, string $idBase);
}
