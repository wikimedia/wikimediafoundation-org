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

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

/**
 * Class AuthFactory
 * @package Inpsyde\MultilingualPress\Framework\Auth
 */
class AuthFactory
{
    /**
     * Create and Auth instance
     *
     * @param Nonce $nonce
     * @param Capability $capability
     * @return Auth
     */
    public function create(Nonce $nonce, Capability $capability): Auth
    {
        return new RequestAuth($nonce, $capability);
    }
}
