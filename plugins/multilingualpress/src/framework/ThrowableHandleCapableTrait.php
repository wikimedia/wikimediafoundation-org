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

namespace Inpsyde\MultilingualPress\Framework;

use Throwable;

use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Trait ThrowableHandleCapableTrait
 * @package Inpsyde\MultilingualPress\Framework
 */
trait ThrowableHandleCapableTrait
{
    /**
     * @param Throwable $throwable
     * @throws Throwable
     */
    protected function handleThrowable(Throwable $throwable)
    {
        $this->logThrowable($throwable);

        if (isWpDebugMode()) {
            throw $throwable;
        }
    }

    /**
     * @param Throwable $throwable
     */
    protected function logThrowable(Throwable $throwable)
    {
        do_action(
            \Inpsyde\MultilingualPress\ACTION_LOG,
            'error',
            $throwable->getMessage(),
            compact('throwable')
        );
    }
}
