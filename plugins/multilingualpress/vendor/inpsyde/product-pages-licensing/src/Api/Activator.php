<?php # -*- coding: utf-8 -*-

/*
 * This file is part of the Product License package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\ProductPagesLicensing\Api;

use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\ProductPagesLicensing\License;
use Inpsyde\ProductPagesLicensing\RequestHandler;
use Psr\Http\Client\ClientExceptionInterface;

class Activator
{
    const WC_API = 'wc-am-api';

    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * @var array
     */
    private $apiConfiguration;

    /**
     * @param RequestHandler $requestHandler
     * @param array $apiConfiguration
     */
    public function __construct(
        RequestHandler $requestHandler,
        array $apiConfiguration
    ) {
        $this->requestHandler = $requestHandler;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * @param License $license
     * @return array
     * @throws ClientExceptionInterface
     */
    public function activate(License $license)
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

        $request = $this->requestHandler->doRequest(
            'GET',
            $url
        );

        $responseBody = json_decode($request);

        if (isset($responseBody->error)) {
            return [
                'error' => $responseBody->error,
                'code' => $responseBody->code,
            ];
        }

        $activated = isset($responseBody->activated) && $responseBody->activated === true
            ? 'active' : 'inactive';

        return [
            'status' => $activated,
        ];
    }

    /**
     * @param License $license
     * @return array
     * @throws ClientExceptionInterface
     */
    public function deactivate($license)
    {
        $args = [
            'wc-api' => self::WC_API,
            'wc_am_action' => 'deactivate',
            'instance' => $license->instance(),
            'product_id' => $license->productId(),
            'api_key' => $license->apiKey(),
        ];

        $url = add_query_arg($args, $this->apiConfiguration['license_api_url']);

        $request = $this->requestHandler->doRequest(
            'GET',
            $url
        );

        $responseBody = json_decode($request);

        if (isset($responseBody->error)) {
            return [
                'error' => $responseBody->error,
                'code' => $responseBody->code,
            ];
        }

        $deactivated = isset($responseBody->deactivated) && $responseBody->deactivated === true ? 'inactive' : '';

        return [
            'status' => $deactivated,
        ];
    }

    /**
     * @param License $license
     * @return array
     * @throws ClientExceptionInterface
     */
    public function status(License $license)
    {
        $args = [
            'wc-api' => self::WC_API,
            'wc_am_action' => 'status',
            'instance' => $license->instance(),
            'product_id' => $license->productId(),
            'api_key' => $license->apiKey(),
        ];

        $url = add_query_arg($args, $this->apiConfiguration['license_api_url']);

        $request = $this->requestHandler->doRequest(
            'GET',
            $url
        );

        $responseBody = json_decode($request);

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
