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

namespace Inpsyde\MultilingualPress\Framework\Language;

/**
 * Interface for all language data type implementations.
 */
interface Language
{
    const ISO_SHORTEST = 'iso_shortest';

    /**
     * Returns the ID of the language.
     *
     * @return int
     */
    public function id(): int;

    /**
     * Checks if the language is written right-to-left (RTL).
     *
     * @return bool
     */
    public function isRtl(): bool;

    /**
     * Returns the language name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Returns the language name.
     *
     * @return string
     */
    public function englishName(): string;

    /**
     * Returns the language name.
     *
     * @return string
     */
    public function nativeName(): string;

    /**
     * Returns the language ISO 639 code.
     *
     * @param string $which
     * @return string
     */
    public function isoCode(string $which = self::ISO_SHORTEST): string;

    /**
     * Returns the language name to be used for frontend purposes.
     *
     * @return string
     */
    public function isoName(): string;

    /**
     * Returns the language BCP-47 tag.
     *
     * @return string
     */
    public function bcp47tag(): string;

    /**
     * Returns the language locale.
     *
     * @return string
     */
    public function locale(): string;
}
