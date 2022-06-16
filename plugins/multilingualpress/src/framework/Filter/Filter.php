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

namespace Inpsyde\MultilingualPress\Framework\Filter;

/**
 * Interface for all filter implementations.
 */
interface Filter
{
    const DEFAULT_ACCEPTED_ARGS = 1;
    const DEFAULT_PRIORITY = 10;

    /**
     * Returns the number of accepted arguments.
     *
     * @return int
     */
    public function acceptedArgs(): int;

    /**
     * Removes the filter.
     *
     * @return bool
     */
    public function disable(): bool;

    /**
     * Adds the filter.
     *
     * @return bool
     */
    public function enable(): bool;

    /**
     * Returns the hook name.
     *
     * @return string
     */
    public function hook(): string;

    /**
     * Returns the callback priority.
     *
     * @return int
     */
    public function priority(): int;
}
