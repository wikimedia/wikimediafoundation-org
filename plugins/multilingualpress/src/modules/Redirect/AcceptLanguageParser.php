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

namespace Inpsyde\MultilingualPress\Module\Redirect;

/**
 * Parser for Accept-Language headers, sorting by priority.
 */
class AcceptLanguageParser
{
    /**
     * Parses the given Accept header and returns the according data in array form and returns
     * an array with language codes as keys, and priorities as values.
     *
     * @param string $header
     * @return float[]
     */
    public function parseHeader(string $header): array
    {
        $header = $this->removeHeaderComment($header);
        if ('' === $header) {
            return [];
        }

        $values = [];
        foreach ($this->headerValues($header) as $value) {
            list($language, $priority) = $this->splitValue($value);
            is_string($language) and $values[$language] = (float)$priority;
        }

        return $values;
    }

    /**
     * Returns the given Accept header without comment.
     *
     * A comment starts with a `(` and ends with the first `)`.
     *
     * @param string $header
     * @return string
     */
    private function removeHeaderComment(string $header): string
    {
        $delimiter = '~';

        $delimiterFound = false !== strpos($header, $delimiter);
        if ($delimiterFound) {
            $header = str_replace($delimiter, "\\{$delimiter}", $header);
        }

        $header = (string)preg_replace('~\([^)]*\)~', '', $header);

        if ($delimiterFound) {
            $header = str_replace("\\{$delimiter}", $delimiter, $header);
        }

        return trim($header);
    }

    /**
     * Returns the array with the individual values of the given Accept header.
     *
     * @param string $headerString
     * @return string[]
     */
    private function headerValues(string $headerString): array
    {
        $values = explode(',', $headerString);
        $values = array_map('trim', $values);

        return $values;
    }

    /**
     * Returns the array with the language and priority of the given value, and an empty array for
     * an invalid language.
     *
     * @param string $value
     * @return array
     */
    private function splitValue(string $value): array
    {
        $language = strtok($value, ';');
        if (!preg_match('~[a-zA-Z_-]~', $language)) {
            return [null, null];
        }

        if ($language === $value) {
            return [$language, 1];
        }

        strtok('=');

        $priority = min(1, max(0, (float)strtok(';')));

        return [$language, $priority];
    }
}
