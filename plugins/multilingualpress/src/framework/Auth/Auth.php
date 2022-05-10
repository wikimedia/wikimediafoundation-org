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

namespace Inpsyde\MultilingualPress\Framework\Auth;

/**
 * @package MultilingualPress
 * @license http://opensource.org/licenses/MIT MIT
 */
interface Auth
{
    /**
     * Check if the current http request is authorized
     *
     * @return bool
     */
    public function isAuthorized(): bool;
}
