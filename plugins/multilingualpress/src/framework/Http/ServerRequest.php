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

namespace Inpsyde\MultilingualPress\Framework\Http;

/**
 * Interface for all HTTP server request abstraction implementations.
 */
interface ServerRequest extends Request
{
    /**
     * Returns a server value.
     *
     * @param string $name
     * @return string
     */
    public function serverValue(string $name): string;
}
