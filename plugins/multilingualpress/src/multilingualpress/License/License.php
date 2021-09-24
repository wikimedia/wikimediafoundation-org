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

namespace Inpsyde\MultilingualPress\License;

class License
{
    private $productId;
    private $apiKey;
    private $instance;
    private $status;

    public function __construct(string $productId, string $apiKey, string $instance, string $status)
    {
        $this->productId = $productId;
        $this->apiKey = $apiKey;
        $this->instance = $instance;
        $this->status = $status;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }

    public function instance(): string
    {
        return $this->instance;
    }

    public function status(): string
    {
        return $this->status;
    }
}
