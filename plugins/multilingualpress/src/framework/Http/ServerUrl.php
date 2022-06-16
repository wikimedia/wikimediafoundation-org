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

/**
 * URL implementation that is build starting from server data as array.
 */
final class ServerUrl implements Url
{
    /**
     * @var array
     */
    private $serverData;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $host;

    /**
     * @param array $serverData
     * @param string $host
     */
    public function __construct(array $serverData, string $host = '')
    {
        $this->serverData = $serverData;
        $this->host = $host;
    }

    /**
     * Returns the URL string.
     *
     * @return string
     */
    public function __toString(): string
    {
        $this->ensureUrl();

        return $this->url;
    }

    /**
     * Extract URL from server data and stores in object properties if not set yet.
     */
    private function ensureUrl()
    {
        if (null !== $this->url) {
            return;
        }

        list($host, $port) = $this->hostAndPort();
        if (!$host) {
            $this->url = '';

            return;
        }

        list($path, $fragment, $query) = $this->pathFragmentAndQuery();

        $scheme = is_ssl() ? 'https' : 'http';

        $this->url = rtrim("{$scheme}://{$host}", '/');

        if ($port && 80 !== $port) {
            $this->url .= ":{$port}";
        }

        $this->url .= '/' . trim($path, '/');
        $fragment and  $this->url .= "#{$fragment}";
        $query and  $this->url .= "?{$query}";
    }

    /**
     * @return array
     */
    private function hostAndPort(): array
    {
        if ($this->host) {
            $host = $this->host;
            $port = 80;

            if (preg_match('|\:(\d+)$|', $host, $matches)) {
                $host = substr($host, 0, -1 * (strlen($matches[1]) + 1));
                $port = (int)$matches[1];
            }

            return [$host, $port];
        }

        $serverName = $this->serverData['SERVER_NAME'] ?? null;

        if (!$serverName) {
            return ['', 80];
        }

        $host = $serverName;
        $serverPort = $this->serverData['SERVER_PORT'] ?? null;
        $port = is_numeric($serverPort) ? (int)$serverPort : 80;
        $serverAddress = $this->serverData['SERVER_ADDR'] ?? null;

        if (
            !is_string($serverAddress)
            || !preg_match('/^\[[0-9a-fA-F\:]+\]$/', $host)
        ) {
            return [$host, $port];
        }

        // Misinterpreted IPv6-Address reported for Safari on Windows.
        $portPosition = strrpos("[{$serverAddress}]", ':') + 1;
        if ("{$port}]" === substr("[{$serverAddress}]", $portPosition)) {
            $port = 80;
        }

        return [$host, $port];
    }

    /**
     * @return array
     */
    private function pathFragmentAndQuery(): array
    {
        $path = $this->path();

        $queryPosition = strpos($path, '?');
        if (false !== $queryPosition) {
            $path = substr($path, 0, $queryPosition);
        }

        $fragment = '';
        if (strpos($path, '#') !== false) {
            list($path, $fragment) = explode('#', $path, 2);
        }

        $queryString = $this->serverData['QUERY_STRING'] ?? null;
        $query = '';
        if (is_string($queryString) && $queryString) {
            $query = ltrim($queryString, '?');
        }

        return [$path, $fragment, $query];
    }

    /**
     * @return string
     */
    private function path(): string
    {
        // IIS7 with URL Rewrite: make sure we get the unencoded url.
        $iisUrlRewritten = $this->serverData['IIS_WasUrlRewritten'] ?? null;
        $unencodedUrl = $this->serverData['UNENCODED_URL'] ?? null;

        if (
            (int)$iisUrlRewritten === 1
            && is_string($unencodedUrl)
            && $unencodedUrl
        ) {
            return $unencodedUrl;
        }

        $requestUri = $this->serverData['REQUEST_URI'] ?? null;
        $httpRewriteUrl = $this->serverData['HTTP_X_REWRITE_URL'] ?? null;

        if ($httpRewriteUrl !== null) {
            $requestUri = $httpRewriteUrl;
        }

        // Check for IIS 7.0 or later with ISAPI_Rewrite.
        $httpOriginalUrl = $this->serverData['HTTP_X_ORIGINAL_URL'] ?? null;
        if (null !== $httpOriginalUrl) {
            $requestUri = $httpOriginalUrl;
        }

        if (is_string($requestUri) && $requestUri) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }

        $origPathInfo = $this->serverData['ORIG_PATH_INFO'] ?? null;

        return is_string($origPathInfo) && $origPathInfo ? $origPathInfo : '/';
    }
}
