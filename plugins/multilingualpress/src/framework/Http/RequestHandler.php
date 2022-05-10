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

namespace Inpsyde\MultilingualPress\Framework\Http;

/**
 * Interface RequestHandler
 * @package Inpsyde\MultilingualPress\Framework\Http
 */
interface RequestHandler
{
    /**
     * @param ServerRequest $request
     */
    public function handle(ServerRequest $request);
}
