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

use Inpsyde\ProductPagesLicensing\License;
use Inpsyde\ProductPagesLicensing\RequestHandler;
use Psr\Http\Client\ClientExceptionInterface;
use stdClass;

class Updater
{
    const WC_API = 'wc-am-api';

    /**
     * @var array
     */
    private $pluginData;

    /**
     * @var array
     */
    private $apiConfiguration;

    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * @var License
     */
    private $license;

    /**
     * @param array $pluginProperties
     * @param array $apiConfiguration
     * @param RequestHandler $requestHandler
     * @param License $license
     */
    public function __construct(
        array $pluginProperties,
        array $apiConfiguration,
        RequestHandler $requestHandler,
        License $license
    ) {
        $this->pluginData = $pluginProperties;
        $this->apiConfiguration = $apiConfiguration;
        $this->requestHandler = $requestHandler;
        $this->license = $license;
    }

    /**
     * @param stdClass $transient
     * @return stdClass
     * @throws ClientExceptionInterface
     */
    public function updateCheck(stdClass $transient)
    {
        if (!did_action('load-update-core.php')) {
            return $transient;
        }

        if ($this->license->status() !== 'active') {
            return $transient;
        }

        $args = [
            'wc-api' => self::WC_API,
            'wc_am_action' => 'update',
            'instance' => $this->license->instance(),
            'api_key' => $this->license->apiKey(),
            'product_id' => $this->license->productId(),
            'version' => $this->pluginData['version'],
            'plugin_name' => $this->pluginData['basename'],
            'slug' => $this->pluginData['slug'],
        ];

        $url = add_query_arg($args, $this->apiConfiguration['license_api_url']);

        $request = $this->requestHandler->doRequest(
            'GET',
            $url
        );

        $responseBody = json_decode($request);

        if (isset($responseBody->error)) {
            return $transient;
        }

        if (isset($responseBody->data->package->new_version)) {
            if (version_compare(
                $responseBody->data->package->new_version,
                $this->pluginData['version'],
                '>'
            )) {
                $pluginBaseName = $this->pluginData['basename'];
                $transient->response[$pluginBaseName] = $responseBody->data->package;
            }
        }

        return $transient;
    }

    /**
     * @param bool|mixed $result
     * @param string $action
     * @param stdClass $args
     * @return bool|mixed
     * @throws ClientExceptionInterface
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function pluginInformation($result, string $action, stdClass $args)
    {
        // phpcs:enable

        if ($action !== 'plugin_information' || $args->slug !== $this->pluginData['slug']) {
            return $result;
        }

        if ($this->license->status() !== 'active') {
            return $result;
        }

        $args = [
            'wc-api' => self::WC_API,
            'wc_am_action' => 'information',
            'instance' => $this->license->instance(),
            'api_key' => $this->license->apiKey(),
            'product_id' => $this->license->productId(),
            'version' => $this->pluginData['version'],
            'plugin_name' => $this->pluginData['basename'],
        ];

        $url = add_query_arg($args, $this->apiConfiguration['license_api_url']);

        $request = $this->requestHandler->doRequest(
            'GET',
            $url
        );

        $responseBody = json_decode($request);

        if (isset($responseBody->data->info->sections)) {
            $responseBody->data->info->sections = (array)$responseBody->data->info->sections;

            return $responseBody->data->info;
        }

        return $result;
    }
}
