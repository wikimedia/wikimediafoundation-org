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
 * Interface for all style data type implementations.
 */
interface Style extends Asset
{
    /**
     * @param string $conditional
     * @return Style
     */
    public function addConditional(string $conditional): Style;

    /**
     * @return string
     */
    public function media(): string;
}
