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
use Inpsyde\MultilingualPress\Framework\Asset\Script;

/**
 * Default script data type implementation.
 */
final class StandardWpScript implements Script
{

    /**
     * @var array[]
     */
    private $data = [];

    /**
     * @var string[]
     */
    private $dependencies;

    /**
     * @var string
     */
    private $handle;

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
     */
    public function __construct(
        string $handle,
        AssetLocation $location,
        array $dependencies = [],
        string $version = null
    ) {

        $this->handle = $handle;
        $this->location = $location;
        $this->dependencies = array_map('strval', $dependencies);
        $this->version = $version;
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
    public function addData(string $jsObjectName, array $jsObjectData): Script
    {
        $this->data[$jsObjectName] = $jsObjectData;

        return $this;
    }

    /**
     * Clears the data so it won't be output another time.
     *
     * @return Script
     */
    public function clearData(): Script
    {
        $this->data = [];

        return $this;
    }

    /**
     * Returns all data to be made available for the script.
     *
     * @return array[]
     */
    public function data(): array
    {
        return $this->data;
    }
}
