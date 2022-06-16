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

namespace Inpsyde\MultilingualPress\Framework\Admin;

/**
 * Interface for all settings page tab data implementations.
 */
interface SettingsPageTabDataAccess
{

    /**
     * Returns the capability.
     *
     * @return string
     */
    public function capability(): string;

    /**
     * Returns the ID.
     *
     * @return string
     */
    public function id(): string;

    /**
     * Returns the slug.
     *
     * @return string
     */
    public function slug(): string;

    /**
     * Returns the title.
     *
     * @return string
     */
    public function title(): string;
}
