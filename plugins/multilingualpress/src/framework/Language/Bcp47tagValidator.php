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
 * Trait Bcp47tagValidator
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
 */
trait Bcp47tagValidator
{
    /**
     * Pattern to test against
     *
     * @var string
     * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
     */
    private $pattern = '/^(?<grandfathered>(?:en-GB-oed|i-(?:ami|bnn|default|enochian|hak|klingon|lux|mingo|navajo|pwn|t(?:a[oy]|su))|sgn-(?:BE-(?:FR|NL)|CH-DE))|(?:art-lojban|cel-gaulish|no-(?:bok|nyn)|zh-(?:guoyu|hakka|min(?:-nan)?|xiang)))|(?:(?<language>(?:[A-Za-z]{2,3}(?:-(?<extlang>[A-Za-z]{3}(?:-[A-Za-z]{3}){0,2}))?)|[A-Za-z]{4}|[A-Za-z]{5,8})(?:-(?<script>[A-Za-z]{4}))?(?:-(?<region>[A-Za-z]{2}|[0-9]{3}))?(?:-(?<variant>[A-Za-z0-9]{5,8}|[0-9][A-Za-z0-9]{3}))*(?:-(?<extension>[0-9A-WY-Za-wy-z](?:-[A-Za-z0-9]{2,8})+))*)(?:-(?<privateUse>x(?:-[A-Za-z0-9]{1,8})+))?$/Di';
    // phpcs:enable

    /**
     * Validate bcp47Tag
     *
     * @param string $bcp47Tag
     * @return bool
     */
    protected function validate(string $bcp47Tag): bool
    {
        $matched = (bool)preg_match($this->pattern, $bcp47Tag, $matches);

        return $matched;
    }
}
