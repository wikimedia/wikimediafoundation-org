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

namespace Inpsyde\MultilingualPress\SiteFlags\Flag;

use Inpsyde\MultilingualPress\Framework\Language\Language;

/**
 * Class Raster
 *
 * @package Inpsyde\MultilingualPress\SiteFlags\Flag
 */
final class Raster implements Flag
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @var string
     */
    private $url;

    /**
     * Raster constructor
     * @param Language $language
     * @param string $url
     */
    public function __construct(Language $language, string $url)
    {
        $this->language = $language;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function markup(): string
    {
        $alt = sprintf(
            __('%s language flag', 'multilingualpress'),
            $this->language->nativeName()
        );

        return sprintf(
            '<img src="%1$s" alt="%2$s" />',
            esc_url($this->url()),
            esc_attr($alt)
        );
    }
}
