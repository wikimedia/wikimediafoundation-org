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

/**
 * Version number implementation according to the SemVer specification.
 *
 * @see http://semver.org/#semantic-versioning-specification-semver
 */
class SemanticVersionNumber
{
    const FALLBACK_VERSION = '0.0.0';

    /**
     * @var string
     */
    private $version;

    /**
     * @param string $version
     */
    public function __construct(string $version)
    {
        $this->version = $this->normalize($version);
    }

    /**
     * Returns the version string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->version;
    }

    /**
     * Formats the given number according to the Semantic Versioning specification.
     *
     * @param string $version
     * @return string
     *
     * @see http://semver.org/#semantic-versioning-specification-semver
     */
    private function normalize(string $version): string
    {
        list($number, $preRelease, $meta) = $this->matchSemverPattern($version);

        if (!$number) {
            return self::FALLBACK_VERSION;
        }

        $version = $number;

        if ($preRelease) {
            $version .= "-{$preRelease}";
        }

        if ($meta) {
            $version .= "+{$meta}";
        }

        return $version;
    }

    /**
     * Returns a 3 items array with the 3 parts of SemVer specs, in order:
     * - The numeric part of SemVer specs
     * - The pre-release part of SemVer specs, could be empty
     * - The meta part of SemVer specs, could be empty.
     *
     * @param string $version
     * @return string[]
     */
    private function matchSemverPattern(string $version): array
    {
        $pattern = '~^(?P<numbers>(?:[0-9]+)+(?:[0-9\.]+)?)+(?P<anything>.*?)?$~';
        $matched = preg_match($pattern, $version, $matches);

        if (!$matched) {
            return ['', '', ''];
        }

        $numbers = explode('.', trim($matches['numbers'], '.'));

        // if less than 3 numbers, ensure at least 3 numbers, filling with zero
        $numeric = implode(
            '.',
            array_replace(
                ['0', '0', '0'],
                array_slice($numbers, 0, 3)
            )
        );

        // if more than 3 numbers, store additional numbers as build.
        $build = implode('.', array_slice($numbers, 3));

        // if there's nothing else, we already know what to return.
        if (!$matches['anything']) {
            return [$numeric, $build, ''];
        }

        $pre = ltrim($matches['anything'], '-');
        $meta = '';

        // seems we have some metadata.
        if (substr_count($matches['anything'], '+') > 0) {
            $parts = explode('+', $pre);
            // pre is what's before the first +, which could actually be empty
            // when version has meta but not pre-release.
            $pre = array_shift($parts);
            // everything comes after first + is meta.
            // If there were more +, we replace them with dots.
            $meta = $this->sanitizeIdentifier(trim(implode('.', $parts), '-'));
        }

        if ($build) {
            $pre = "{$build}.{$pre}";
        }

        return [$numeric, $this->sanitizeIdentifier($pre), $meta];
    }

    /**
     * Sanitizes given identifier according to SemVer specs.
     * Allow for underscores, replacing them with hyphens.
     *
     * @param string $identifier
     * @return string
     */
    private function sanitizeIdentifier(string $identifier): string
    {
        // the condition will be false for both "" and "0", which are both valid
        // so don't need any replace.
        if ($identifier) {
            $identifier = (string)preg_replace(
                '~[^a-zA-Z0-9\-\.]~',
                '',
                str_replace('_', '-', $identifier)
            );
        }

        return $identifier;
    }
}
