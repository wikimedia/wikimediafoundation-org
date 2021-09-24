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

namespace Inpsyde\MultilingualPress\Framework\Message;

/**
 * Interface Message
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
interface MessageInterface
{
    /**
     * Message Content
     *
     * @return string
     */
    public function content(): string;

    /**
     * Response Data
     *
     * @return array
     */
    public function data(): array;

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @param string $type
     * @return bool
     */
    public function isOfType(string $type): bool;
}
