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

use function Inpsyde\MultilingualPress\isScriptDebugMode;

/**
 * Asset URL data type implementation aware of debug mode and thus potentially
 * minified asset files.
 */
final class MaybeMinifiedAssetUrl implements AssetUrl
{
    /**
     * @var string
     */
    private $url = '';

    /**
     * @var string
     */
    private $version = '';

    /**
     * Returns a new URL object, instantiated according to the given location object.
     *
     * @param AssetLocation $location
     * @return MaybeMinifiedAssetUrl
     */
    public static function fromLocation(AssetLocation $location): MaybeMinifiedAssetUrl
    {
        return new static(
            $location->file(),
            $location->path(),
            $location->url()
        );
    }

    /**
     * @param string $filename
     * @param string $dirPath
     * @param string $dirUrl
     */
    public function __construct(string $filename, string $dirPath, string $dirUrl)
    {
        $dirPath = rtrim($dirPath, '/');
        $filename = $this->filename($filename, $dirPath);
        $fullpath = "{$dirPath}/{$filename}";
        if (is_readable($fullpath)) {
            $this->url = rtrim($dirUrl, '/') . "/{$filename}";
            $this->version = (string)filemtime($fullpath);
        }
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * Returns the name of the minified version of the given file if it exists
     * and not debugging, otherwise the unmodified file.
     *
     * @param string $filename
     * @param string $dirPath
     * @return string
     */
    private function filename(string $filename, string $dirPath): string
    {
        if (isScriptDebugMode()) {
            return $filename;
        }

        $minified = $this->minified($filename);

        if ($minified === $filename) {
            return $filename;
        }

        if (is_readable("{$dirPath}/{$minified}")) {
            return $minified;
        }

        return $filename;
    }

    /**
     * Returns the given file with ".min" infix, if not there already.
     *
     * @param string $file
     * @return string
     */
    private function minified(string $file): string
    {
        // Check for already minified file.
        if (preg_match('~\.min\.[^.]+$~', $file)) {
            return $file;
        }

        return preg_replace('~\.[^.]+$~', '.min$0', $file);
    }
}
