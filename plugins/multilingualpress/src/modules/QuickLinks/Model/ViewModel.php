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

namespace Inpsyde\MultilingualPress\Module\QuickLinks\Model;

/**
 * Interface ViewModel
 * @package Inpsyde\MultilingualPress\Core\Setting
 */
interface ViewModel
{
    /**
     * The ID of the Model
     *
     * @return string
     */
    public function id(): string;

    /**
     * Print the Title for the Setting
     *
     * @return void
     */
    public function title();

    /**
     * Print the Settings
     *
     * @return void
     */
    public function render();
}
