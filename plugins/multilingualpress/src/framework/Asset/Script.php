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
 * Interface for all script data type implementations.
 */
interface Script extends Asset
{

    /**
     * Makes the given data available for the script.
     *
     * @param string $jsObjectName
     * @param array $jsObjectData
     * @return Script
     */
    public function addData(string $jsObjectName, array $jsObjectData): Script;

    /**
     * Returns all data to be made available for the script.
     *
     * @return array[]
     */
    public function data(): array;
}
