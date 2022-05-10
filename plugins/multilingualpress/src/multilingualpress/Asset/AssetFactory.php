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

namespace Inpsyde\MultilingualPress\Asset;

use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetLocation;
use Inpsyde\MultilingualPress\Framework\Asset\Script;
use Inpsyde\MultilingualPress\Framework\Asset\Style;

/**
 * Factory for various asset objects.
 */
class AssetFactory
{
    /**
     * @var string
     */
    private $internalScriptPath;

    /**
     * @var string
     */
    private $internalScriptUrl;

    /**
     * @var string
     */
    private $internalStylePath;

    /**
     * @var string
     */
    private $internalStyleUrl;

    /**
     * @param Locations $locations
     */
    public function __construct(Locations $locations)
    {
        $this->internalScriptPath = $locations->valueFor('js', Locations::TYPE_PATH);
        $this->internalScriptUrl = $locations->valueFor('js', Locations::TYPE_URL);
        $this->internalStylePath = $locations->valueFor('css', Locations::TYPE_PATH);
        $this->internalStyleUrl = $locations->valueFor('css', Locations::TYPE_URL);
    }

    /**
     * Returns a new script object, instantiated according to the given arguments.
     *
     * @param string $handle
     * @param string $file
     * @param string[] $dependencies
     * @param string|null $version
     * @return Script
     */
    public function createInternalScript(
        string $handle,
        string $file,
        array $dependencies = [],
        string $version = null
    ): Script {

        return new StandardWpScript(
            $handle,
            new AssetLocation($file, $this->internalScriptPath, $this->internalScriptUrl),
            $dependencies,
            $version
        );
    }

    /**
     * Returns a new style object, instantiated according to the given arguments.
     *
     * @param string $handle
     * @param string $file
     * @param string[] $dependencies
     * @param string|null $version
     * @param string $media
     * @return Style
     */
    public function createInternalStyle(
        string $handle,
        string $file,
        array $dependencies = [],
        string $version = null,
        string $media = 'all'
    ): Style {

        return new StandardWpStyle(
            $handle,
            new AssetLocation($file, $this->internalStylePath, $this->internalStyleUrl),
            $dependencies,
            $version,
            $media
        );
    }
}
