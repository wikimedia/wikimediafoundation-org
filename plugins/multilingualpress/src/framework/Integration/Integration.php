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

namespace Inpsyde\MultilingualPress\Framework\Integration;

/**
 * Interface for all integration controllers.
 */
interface Integration
{

    /**
     * Integrates some (possibly external) service with MultilingualPress.
     *
     * @return bool
     */
    public function integrate(): bool;
}
