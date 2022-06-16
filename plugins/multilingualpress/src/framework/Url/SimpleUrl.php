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

use InvalidArgumentException;

/**
 * Class Url
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
 */
class SimpleUrl implements Url
{
    /**
     * @var string
     */
    private $url;

    /**
     * SimpleUrl constructor.
     * @param string $url
     * @throws InvalidArgumentException
     */
    public function __construct(string $url)
    {
        $isValidUrl = filter_var($url, FILTER_VALIDATE_URL);
        if (!$isValidUrl) {
            throw new InvalidArgumentException('Invalid Url Given.');
        }

        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->url;
    }
}
