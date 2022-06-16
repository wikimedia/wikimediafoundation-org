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

namespace Inpsyde\MultilingualPress\Framework\Http;

/**
 * phpcs:disable WordPress.VIP.SuperGlobalInputUsage
 * phpcs:disable WordPress.CSRF
 */
class RequestGlobalsManipulator
{
    const METHOD_GET = 'GET';

    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    private $requestMethod;

    /**
     * @var array
     */
    private $storage = [];

    /**
     * @param string $requestMethod
     */
    public function __construct(string $requestMethod = self::METHOD_POST)
    {
        $this->requestMethod = self::METHOD_POST === strtoupper($requestMethod)
            ? self::METHOD_POST
            : self::METHOD_GET;
    }

    /**
     * Removes all data from the request globals.
     *
     * @return int
     */
    public function clear(): int
    {
        $name = "_{$this->requestMethod}";
        if (empty($GLOBALS[$name])) {
            return 0;
        }

        $this->storage = $GLOBALS[$name];
        $_REQUEST = array_diff_key($_REQUEST, $this->storage);
        $GLOBALS[$name] = [];

        return count($this->storage);
    }

    /**
     * Restores all data from the storage.
     *
     * @return int
     */
    public function restore(): int
    {
        if (!$this->storage) {
            return 0;
        }

        $_REQUEST = array_merge($_REQUEST, $this->storage);
        $name = "_{$this->requestMethod}";
        $GLOBALS[$name] = $this->storage;
        $this->storage = [];

        return count($GLOBALS[$name]);
    }
}
