<?php # -*- coding: utf-8 -*-

/*
 * This file is part of the Product License package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\ProductPagesLicensing;

class License
{
    private $productId;
    private $apiKey;
    private $instance;
    private $status;

    public function __construct($productId, $apiKey, $instance, $status)
    {
        $this->productId = $productId;
        $this->apiKey = $apiKey;
        $this->instance = $instance;
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function productId()
    {
        return $this->productId;
    }

    /**
     * @return mixed
     */
    public function apiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return mixed
     */
    public function instance()
    {
        return $this->instance;
    }

    /**
     * @return mixed
     */
    public function status()
    {
        return $this->status;
    }
}

