<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This file incorporates work from Zend Framework "zend-diactoros" released under New BSD License
 * and covered by the following copyright and permission notices:
 *
 * Copyright (c) Zend Technologies USA Inc. (http://www.zend.com)
 *
 * @see https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md
 * @see https://github.com/zendframework/zend-diactoros/blob/master/src/ServerRequestFactory.php
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Framework\Http;

use Inpsyde\MultilingualPress\Framework\Url\Url;
use RangeException;

final class PhpServerRequest implements ServerRequest
{
    /**
     * @var array|null
     */
    private static $values;

    /**
     * @var array|null
     */
    private static $headers;

    /**
     * @var array|null
     */
    private static $server;

    /**
     * @var Url|null
     */
    private static $url;

    /**
     * @var string|null
     */
    private static $body;

    const INPUT_SOURCES = [
        INPUT_POST => Request::INPUT_POST,
        INPUT_GET => Request::INPUT_GET,
        INPUT_REQUEST => Request::INPUT_REQUEST,
        INPUT_COOKIE => Request::INPUT_COOKIE,
        INPUT_SERVER => Request::INPUT_SERVER,
        INPUT_ENV => Request::INPUT_ENV,
    ];

    /**
     * Returns the URL for current request.
     *
     * @return Url
     */
    public function url(): Url
    {
        $this->ensureUrl();

        return self::$url;
    }

    /**
     * @inheritdoc
     */
    public function body(): string
    {
        $this->ensureBody();

        return self::$body;
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function bodyValue(
        string $name,
        int $method = Request::INPUT_REQUEST,
        int $filter = FILTER_UNSAFE_RAW,
        int $options = FILTER_FLAG_NONE
    ) {
        // phpcs:enable
        $method = $this->normalizeInputSource($method);
        $this->ensureValues();

        if (!isset(self::$values[$method][$name])) {
            return null;
        }

        $value = self::$values[$method][$name];

        return filter_var(
            $value,
            $filter,
            $this->adaptFilterOptions($value, $options)
        );
    }

    /**
     * @inheritdoc
     */
    public function header(string $name): string
    {
        $this->ensureHeaders();

        return (string)(self::$headers[$name] ?? '');
    }

    /**
     * @inheritdoc
     */
    public function serverValue(string $name): string
    {
        $this->ensureServer();

        $name = strtoupper($name);

        return (string)(self::$server[$name] ?? '');
    }

    /**
     * @inheritdoc
     */
    public function method(): string
    {
        $value = $this->serverValue('REQUEST_METHOD');
        if (is_string($value) && $value) {
            $value = strtoupper($value);
        }

        in_array($value, static::METHODS, true) or $value = static::GET;

        return $value;
    }

    /**
     * Ensure request body is available in class property.
     */
    private function ensureBody()
    {
        if (self::$body === null) {
            self::$body = stream_get_contents(fopen('php://input', 'r'));
        }
    }

    /**
     * Ensure server values from request are available in class property.
     */
    private function ensureServer()
    {
        if (null !== self::$server) {
            return;
        }

        // phpcs:disable WordPress.VIP.SuperGlobalInputUsage
        self::$server = $this->maybeUnslash(array_change_key_case($_SERVER, CASE_UPPER));
        if (
            array_key_exists('HTTP_AUTHORIZATION', self::$server)
            || !function_exists('apache_request_headers')
        ) {
            return;
        }
        // phpcs:enable

        // This seems to be the only way to get the Authorization header on Apache.
        $apacheHeaders = apache_request_headers();
        if (!$apacheHeaders) {
            return;
        }

        $apacheHeaders = array_change_key_case($apacheHeaders, CASE_LOWER);

        if (array_key_exists('authorization', $apacheHeaders)) {
            self::$server['HTTP_AUTHORIZATION'] = $apacheHeaders['authorization'];
        }
    }

    /**
     * Ensure headers from request are available in class property.
     */
    private function ensureHeaders()
    {
        if (null !== self::$headers) {
            return;
        }

        $this->ensureServer();

        $headers = [];
        foreach (self::$server as $key => $value) {
            // Apache prefixes env variables with REDIRECT_ if they are added by rewrite rules.
            $prefixed = false;
            if (strpos($key, 'REDIRECT_') === 0) {
                $prefixed = true;
                $key = substr($key, 9);
            }

            // We will not overwrite existing variables with the prefixed versions, though.
            if ($prefixed && array_key_exists($key, self::$server)) {
                continue;
            }

            if ($value && strpos($key, 'HTTP_') === 0) {
                $headers[ucwords(strtolower(str_replace('_', '-', substr($key, 5))), "-")] = $value;
                continue;
            }

            if ($value && strpos($key, 'CONTENT_') === 0) {
                $headers['content-' . strtolower(substr($key, 8))] = $value;
                continue;
            }
        }

        self::$headers = $headers;
    }

    /**
     * Ensure values from request are available in class property.
     */
    private function ensureValues()
    {
        if (null !== self::$values) {
            return;
        }

        $queryData = filter_input_array(Request::INPUT_GET) ?: [];
        self::$values[Request::INPUT_GET] = $queryData;
        $method = $this->method();

        // For GET requests URL query data represent all the request values.
        if ('GET' === $method) {
            self::$values[Request::INPUT_REQUEST] = $queryData;

            return;
        }

        // For POST requests values are represented by URL query data merged with any kind of form data.
        if ($method === 'POST') {
            self::$values[Request::INPUT_POST] = filter_input_array(Request::INPUT_POST) ?: [];
            self::$values[Request::INPUT_REQUEST] = array_merge(
                self::$values[Request::INPUT_GET],
                self::$values[Request::INPUT_POST]
            );

            return;
        }

        $contentType = $this->serverValue('CONTENT_TYPE');

        // When content type is not URL-encoded, give up parsing body.
        // Raw body can still be accessed and decoded.
        if ('application/x-www-form-urlencoded' !== $contentType) {
            self::$values[Request::INPUT_REQUEST] = $queryData;

            return;
        }

        // When not GET nor POST method is used, but content is URL-encoded,
        // we can safely decode raw body stream.
        @parse_str($this->body(), $values);

        self::$values[Request::INPUT_REQUEST] = is_array($values)
            ? array_merge($queryData, $values)
            : $queryData;
    }

    /**
     * Ensure URL marshaled from request is available in class property.
     */
    private function ensureUrl()
    {
        if (!self::$url instanceof Url) {
            $this->ensureHeaders();
            self::$url = new ServerUrl(self::$server, $this->header('HOST'));
        }
    }

    /**
     * Returns the given filter options, potentially adapted to work with array data.
     *
     * @param mixed $value
     * @param mixed $options
     * @return int|array
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    private function adaptFilterOptions($value, $options)
    {
        // phpcs:enable

        if (!is_array($value)) {
            return $options;
        }

        if (!is_array($options)) {
            return $options | FILTER_REQUIRE_ARRAY;
        }

        $flags = (int)($options['flag'] ?? 0) | FILTER_REQUIRE_ARRAY;

        return array_merge($options, compact('flags'));
    }

    /**
     * Right before "sanitize_comment_cookies" action, WordPress calls `wp_magic_quotes` that adds
     * slashes to $_GET, $_POST, $_COOKIE, and $_SERVER.
     * If that happened, we remove slashes to have access to "raw" value, and leave the burden of
     * slashing, if necessary, to client code.
     *
     * @param array $values
     * @return array
     *
     * @see wp_magic_quotes()
     */
    private function maybeUnslash(array $values): array
    {
        if (did_action('sanitize_comment_cookies')) {
            return stripslashes_deep($values);
        }

        return $values;
    }

    /**
     * Normalizes an input source to a known value.
     *
     * Will attempt to convert the source to a standardized known value,
     * if it is registered in the map.
     *
     * @param int|string $source The input source to normalize.
     * @return int The normalized input source.
     * @throws RangeException If cannot normalize.
     */
    protected function normalizeInputSource($source): int
    {
        if (isset(static::INPUT_SOURCES[$source])) {
            $source = static::INPUT_SOURCES[$source];
        }

        if (!in_array($source, static::INPUT_SOURCES)) {
            throw new RangeException(sprintf('Unknown input source "%1$s"', $source));
        }

        return $source;
    }
}
