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

namespace Inpsyde\MultilingualPress\Framework\Asset;

use RuntimeException;

/**
 * Class AssetException
 * @package Inpsyde\MultilingualPress\Framework\Asset
 */
class AssetException extends RuntimeException
{
    /**
     * When trying to enqueue a script not yet registered
     *
     * @param string $handle
     * @return AssetException
     */
    public static function forWhenEnqueuingScriptNotRegistered(string $handle): self
    {
        return new self(
            "Script with handle {$handle} is not registered yet. Cannot enqueue"
        );
    }

    /**
     * Add script data for Script which not exists
     *
     * @param string $handle
     * @return AssetException
     */
    public static function addingScriptDataWhenScriptDoesNotExists(string $handle): self
    {
        return new self(
            "Trying to add script data to a script with handle {$handle} which is not exists."
        );
    }
}
