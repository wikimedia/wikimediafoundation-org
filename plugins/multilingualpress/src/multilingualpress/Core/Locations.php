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

namespace Inpsyde\MultilingualPress\Core;

/**
 * MultilingualPress-specific locations implementation.
 */
class Locations
{
    const TYPE_PATH = 'path';
    const TYPE_URL = 'url';

    /**
     * @var string[][]
     */
    private $locations = [];

    /**
     * Adds a new location according to the given arguments.
     *
     * @param string $name
     * @param string $path
     * @param string $url
     * @return Locations
     */
    public function add(string $name, string $path, string $url): Locations
    {
        $this->locations[$name] = [
            Locations::TYPE_PATH => rtrim($path, '/'),
            Locations::TYPE_URL => rtrim($url, '/') . '/',
        ];

        return $this;
    }

    /**
     * Returns the location data according to the given arguments.
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    public function valueFor(string $name, string $type): string
    {
        return $this->locations[$name][$type] ?? '';
    }

    /**
     * Checks if a location with the given name exists.
     *
     * @param string $name
     * @return bool
     */
    public function contain(string $name): bool
    {
        return array_key_exists($name, $this->locations);
    }
}
