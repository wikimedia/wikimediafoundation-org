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

namespace Inpsyde\MultilingualPress\Framework\Setting;

/**
 * The interface for options of settings.
 */
interface SettingOptionInterface
{
    /**
     * The setting id
     *
     * @return string
     */
    public function id(): string;

    /**
     * The setting value
     *
     * @return string
     */
    public function value(): string;

    /**
     * The setting label
     *
     * @return string
     */
    public function label(): string;
}
