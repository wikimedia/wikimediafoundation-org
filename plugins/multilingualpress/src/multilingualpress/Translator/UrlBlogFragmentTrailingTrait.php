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

namespace Inpsyde\MultilingualPress\Translator;

trait UrlBlogFragmentTrailingTrait
{
    /**
     * @param string $string
     * @return string
     */
    private function untrailingBlogIt(string $string): string
    {
        return preg_replace('|^/?blog|', '', $string);
    }

    /**
     * @param string $string
     * @return string
     */
    private function trailingBlogIt(string $string): string
    {
        $string = $this->untrailingBlogIt($string);

        return '/blog/' . ltrim($string, '/');
    }
}
