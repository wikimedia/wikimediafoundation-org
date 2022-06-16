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

namespace Inpsyde\MultilingualPress\SiteFlags\Flag;

/**
 * Interface Flag
 */
interface Flag
{
    /**
     * @return string
     */
    public function url(): string;

    /**
     * @return string
     */
    public function markup(): string;
}
