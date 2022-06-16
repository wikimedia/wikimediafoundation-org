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

namespace Inpsyde\MultilingualPress\Schedule\Action;

use RuntimeException;

/**
 * Interface ActionTask
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
interface ActionTask
{
    /**
     * @return void
     * @throws RuntimeException if execution fails
     */
    public function execute();
}
