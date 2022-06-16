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

namespace Inpsyde\MultilingualPress\License\Api;

use Inpsyde\MultilingualPress\License\License;

class Activator
{
    const WC_API = 'wc-am-api';

    /**
     * @var array
     */
    private $apiConfiguration;

    /**
     * @param array $apiConfiguration
     */
    public function __construct(array $apiConfiguration)
    {
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * @param License $license
     * @return array
     */
    public function activate(License $license): array
    {
        $args = [
            'wc-api' => self::WC_API,
            'wc_am_action' => 'activate',
            'instance' => $license->instance(),
            'product_id' => $license->productId(),
            'api_key' => $license->apiKey(),
            'object' => str_ireplace(['http://', 'https://'], '', home_url()),
            'version' => $this->apiConfiguration['version'],
        ];

        $url = add_query_arg($args, $this->apiConfiguration['license_api_url']);
        $request = wp_remote_get($url);

        if (is_wp_error($request)) {
            return [
                'error' => $request->get_error_message(),
                'code' => $request->get_error_code(),
            ];
        }

        $responseBody = json_decode($request['body']);

        if (isset($responseBody->error)) {
            return [
                'error' => $responseBody->error,
                'code' => $responseBody->code,
            ];
        }

        $activated = isset($responseBody->activated) && $responseBody->activated === true
            ? 'active'
            : 'inactive';

        return [
            'status' => $activated,
        ];
    }

    /**
     * @param License $license
     * @return array
     */
    public function deactivate(License $license): array
    {
        $args = [
            'wc-api' => self::WC_API,
            'wc_am_action' => 'deactivate',
            'instance' => $license->instance(),
            'product_id' => $license->productId(),
            'api_key' => $license->apiKey(),
        ];

        $url = add_query_arg($args, $this->apiConfiguration['license_api_url']);
        $request = wp_remote_get($url);

        if (is_wp_error($request)) {
            return [
                'error' => $request->get_error_message(),
                'code' => $request->get_error_code(),
            ];
        }

        $responseBody = json_decode($request['body']);

        if (isset($responseBody->error)) {
            return [
                'error' => $responseBody->error,
                'code' => $responseBody->code,
            ];
        }

        $deactivated = isset($responseBody->deactivated) && $responseBody->deactivated === true
            ? 'inactive'
            : '';

        return [
            'status' => $deactivated,
        ];
    }

    /**
     * @param License $license
     * @return array
     */
    public function status(License $license): array
    {
        $args = [
            'wc-api' => self::WC_API,
            'wc_am_action' => 'status',
            'instance' => $license->instance(),
            'product_id' => $license->productId(),
            'api_key' => $license->apiKey(),
        ];

        $url = add_query_arg($args, $this->apiConfiguration['license_api_url']);
        $request = wp_remote_get($url);

        if (is_wp_error($request)) {
            return [
                'error' => $request->get_error_message(),
                'code' => $request->get_error_code(),
            ];
        }

        $responseBody = json_decode($request['body']);

        if (isset($responseBody->error)) {
            return [
                'error' => $responseBody->error,
                'code' => $responseBody->code,
            ];
        }

        $statusCheck = isset($responseBody->status_check) ? $responseBody->status_check : 'inactive';

        return [
            'status' => $statusCheck,
        ];
    }
}
