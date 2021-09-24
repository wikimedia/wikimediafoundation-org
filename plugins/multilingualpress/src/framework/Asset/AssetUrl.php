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

namespace Inpsyde\MultilingualPress\Framework\Asset;

use Inpsyde\MultilingualPress\Framework\Url\Url;

/**
 * Interface for all asset URL data type implementations, providing a file version.
 */
interface AssetUrl extends Url
{

    /**
     * Returns the file version.
     *
     * @return string
     */
    public function version(): string;
}
