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

/**
 * Interface for all asset implementations.
 */
interface Asset
{

    /**
     * Returns the dependencies.
     *
     * @return string[]
     */
    public function dependencies(): array;

    /**
     * Returns the handle.
     *
     * @return string
     */
    public function handle(): string;

    /**
     * Returns the file URL.
     *
     * @return AssetLocation
     */
    public function location(): AssetLocation;

    /**
     * Returns the file version.
     *
     * @return string|null
     */
    public function version();

    /**
     * Returns the handle.
     *
     * @return string
     */
    public function __toString(): string;
}
