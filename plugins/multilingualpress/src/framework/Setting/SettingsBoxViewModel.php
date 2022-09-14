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

namespace Inpsyde\MultilingualPress\Framework\Setting;

/**
 * Interface for all settings box view model implementations.
 */
interface SettingsBoxViewModel
{

    /**
     * Returns the description.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Returns the ID of the container element.
     *
     * @return string
     */
    public function id(): string;

    /**
     * Returns the ID of the form element to be used by the label in order to
     * make it accessible for screen readers.
     *
     * @return string
     */
    public function labelId(): string;

    /**
     * Renders the markup for the settings box.
     */
    public function render();

    /**
     * Returns the title of the settings box.
     *
     * @return string
     */
    public function title(): string;
}
