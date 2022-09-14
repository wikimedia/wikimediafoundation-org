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

namespace Inpsyde\MultilingualPress\Framework\Url;

/**
 * Escaped URL data type.
 */
final class EscapedUrl implements Url
{

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = esc_url($url);
    }

    /**
     * Returns the URL string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->url;
    }
}
