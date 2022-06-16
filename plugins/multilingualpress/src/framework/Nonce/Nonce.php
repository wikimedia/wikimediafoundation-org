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

namespace Inpsyde\MultilingualPress\Framework\Nonce;

/**
 * Interface for all nonce implementations.
 */
interface Nonce
{
    /**
     * Returns the nonce value.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Returns the nonce action.
     *
     * @return string
     */
    public function action(): string;

    /**
     * Checks if the nonce is valid with respect to the given context.
     * Implementation can decide what to do in case of no context given.
     *
     * @param Context|null $context
     * @return bool
     */
    public function isValid(Context $context = null): bool;
}
