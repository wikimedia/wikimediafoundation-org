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

use Inpsyde\MultilingualPress\Framework\Asset\AssetLocation;
use Inpsyde\MultilingualPress\Framework\Asset\Style;

/**
 * Default style data type implementation.
 */
final class StandardWpStyle implements Style
{

    /**
     * @var string[]
     */
    private $dependencies;

    /**
     * @var string
     */
    private $handle;

    /**
     * @var string
     */
    private $media;

    /**
     * @var AssetLocation
     */
    private $location;

    /**
     * @var string|null
     */
    private $version;

    /**
     * @param string $handle
     * @param AssetLocation $location
     * @param array $dependencies
     * @param string|null $version
     * @param string $media
     */
    public function __construct(
        string $handle,
        AssetLocation $location,
        array $dependencies = [],
        string $version = null,
        string $media = 'all'
    ) {

        $this->handle = $handle;
        $this->location = $location;
        $this->dependencies = array_map('strval', $dependencies);
        $this->version = $version;
        $this->media = $media;
    }

    /**
     * @inheritdoc
     */
    public function dependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @inheritdoc
     */
    public function handle(): string
    {
        return $this->handle;
    }

    /**
     * @inheritdoc
     */
    public function location(): AssetLocation
    {
        return $this->location;
    }

    /**
     * @inheritdoc
     * @return string|null
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->handle;
    }

    /**
     * @inheritdoc
     */
    public function addConditional(string $conditional): Style
    {
        wp_style_add_data($this->handle, 'conditional', $conditional);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function media(): string
    {
        return $this->media;
    }
}
