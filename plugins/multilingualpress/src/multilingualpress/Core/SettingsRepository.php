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

namespace Inpsyde\MultilingualPress\Core;

/**
 * Interface for settings repository
 * @psalm-type Setting = array{active?: int, ui?: string}
 */
interface SettingsRepository
{
    /**
     * Get all the settings of current repository stored in site options.
     *
     * Returns a two-items array, where the first is a boolean indicating if settings are found in database,
     * the second is actual settings array. Help distinguish on-purpose empty array in db from a no-result.
     *
     * @return array<int, bool|array>
     * @psalm-return  array{0: bool, 1: array<string, Setting>}
     */
    public function allSettings(): array;
}
