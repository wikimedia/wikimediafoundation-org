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

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\License\Api\Activator;
use Inpsyde\MultilingualPress\License\License;

use function Inpsyde\MultilingualPress\settingsErrors;

class LicenseSettingsUpdater
{
    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Activator
     */
    private $activator;

    /**
     * @param Activator $activator
     * @param Nonce $nonce
     */
    public function __construct(Activator $activator, Nonce $nonce)
    {
        $this->activator = $activator;
        $this->nonce = $nonce;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function updateSettings(Request $request): bool
    {
        if (!$this->nonce->isValid()) {
            return false;
        }

        list($license, $requestType) = $this->licenseFromRequest($request);

        $response = $this->requestTypeActivation($requestType, $license);

        if (isset($response['error'])) {
            settingsErrors(['license' => $response['error']], 'license', 'error');
        }

        return update_network_option(0, 'multilingualpress_license', [
            'license_product_id' => $requestType === 'activation' ? $license->productId() : '',
            'api_key' => $requestType === 'activation' ? $license->apiKey() : '',
            'instance_key' => $license->instance(),
            'status' => isset($response['status']) ? $response['status'] : 'inactive',
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function licenseFromRequest(Request $request): array
    {
        $settings = (array)$request->bodyValue(
            'multilingualpress_license',
            INPUT_POST,
            FILTER_DEFAULT
        );

        $settings = filter_var_array(
            $settings,
            [
                'license_product_id' => [
                    'options' => ['default' => ''],
                    'filter' => FILTER_SANITIZE_STRING,
                ],
                'api_key' => FILTER_SANITIZE_STRING,
                'instance_key' => FILTER_SANITIZE_STRING,
                'deactivate' => [
                    'options' => ['default' => ''],
                    'filter' => FILTER_VALIDATE_BOOLEAN,
                ],
            ]
        );

        $requestFor = $settings['deactivate'] ? 'deactivation' : 'activation';

        if ($requestFor === 'deactivation') {
            $licenseOption = get_network_option(0, 'multilingualpress_license');
            $productId = isset($licenseOption['api_key']) ? $licenseOption['license_product_id'] : '';
            $apiKey = isset($licenseOption['api_key'])
                ? $licenseOption['api_key']
                : '';

            return [
                new License(
                    $productId,
                    $apiKey,
                    $settings['instance_key'],
                    'inactive'
                ),
                $requestFor,
            ];
        }

        return [
            new License(
                $settings['license_product_id'],
                $settings['api_key'],
                $settings['instance_key'],
                'inactive'
            ),
            $requestFor,
        ];
    }

    /**
     * @param string $requestType
     * @param License $license
     * @return array
     */
    private function requestTypeActivation(string $requestType, License $license): array
    {
        if ($requestType === 'activation') {
            return $this->activator->activate($license);
        }

        return $this->activator->deactivate($license);
    }
}
