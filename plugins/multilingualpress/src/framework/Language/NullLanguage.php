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

namespace Inpsyde\MultilingualPress\Framework\Language;

/**
 * Null language implementation.
 */
final class NullLanguage implements Language
{
    /**
     * @inheritdoc
     */
    public function id(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function isRtl(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function englishName(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isoName(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function nativeName(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isoCode(string $which = self::ISO_SHORTEST): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function bcp47tag(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function locale(): string
    {
        return '';
    }
}
