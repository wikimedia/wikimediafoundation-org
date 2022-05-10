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

/**
 * Asset location data type.
 */
class AssetLocation
{

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $file
     * @param string $path
     * @param string $url
     */
    public function __construct(string $file, string $path, string $url)
    {
        $this->file = $file;
        $this->path = $path;
        $this->url = $url;
    }

    /**
     * Returns the relative file name (or path).
     *
     * @return string
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * Returns the local path to the directory containing the file.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Returns the public URL for the directory containing the file.
     *
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }
}
