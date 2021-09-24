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
 * Interface for all registrable widget implementations.
 */
interface Widget
{

    /**
     * Registers the widget.
     *
     * @return bool
     */
    public function register(): bool;
}
