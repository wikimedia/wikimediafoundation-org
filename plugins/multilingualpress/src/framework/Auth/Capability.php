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

namespace Inpsyde\MultilingualPress\Framework\Auth;

/**
 * Interface Capability
 * @package Inpsyde\MultilingualPress\Framework\Http\Auth
 */
interface Capability
{
    /**
     * Check if a capability is valid
     *
     * @return bool True if valid, false otherwise
     */
    public function isValid(): bool;
}
