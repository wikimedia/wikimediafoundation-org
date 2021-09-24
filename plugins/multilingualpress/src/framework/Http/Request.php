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

namespace Inpsyde\MultilingualPress\Framework\Http;

use Inpsyde\MultilingualPress\Framework\Url\Url;

/**
 * Interface for all HTTP request abstraction implementations.
 */
interface Request
{
    const CONNECT = 'CONNECT';
    const DELETE = 'DELETE';
    const GET = 'GET';
    const HEAD = 'HEAD';
    const OPTIONS = 'OPTIONS';
    const PATCH = 'PATCH';
    const POST = 'POST';
    const PUT = 'PUT';
    const TRACE = 'TRACE';

    const INPUT_GET = INPUT_GET;
    const INPUT_POST = INPUT_POST;
    const INPUT_REQUEST = INPUT_REQUEST;
    const INPUT_COOKIE = INPUT_COOKIE;
    const INPUT_SERVER = INPUT_SERVER;
    const INPUT_ENV = INPUT_ENV;

    const METHODS = [
        self::CONNECT,
        self::DELETE,
        self::GET,
        self::HEAD,
        self::OPTIONS,
        self::PATCH,
        self::POST,
        self::PUT,
        self::TRACE,
    ];

    /**
     * Returns the URL for current request.
     *
     * @return Url
     */
    public function url(): Url;

    /**
     * Returns the body of the request as string.
     *
     * @return string
     */
    public function body(): string;

    /**
     * Return a value from request body, optionally filtered.
     *
     * @param string $name
     * @param int $source The input source of the value. One of the `INPUT_*` constants.
     * @param int $filter
     * @param int $options
     * @return mixed
     */
    public function bodyValue(
        string $name,
        int $source = self::INPUT_REQUEST,
        int $filter = FILTER_UNSAFE_RAW,
        int $options = FILTER_FLAG_NONE
    );

    /**
     * Returns header value as set in the request.
     *
     * @param string $name
     * @return string
     */
    public function header(string $name): string;

    /**
     * Returns method (GET, POST..) value as set in the request.
     *
     * @return string
     */
    public function method(): string;
}
