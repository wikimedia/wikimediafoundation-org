<?php # -*- coding: utf-8 -*-
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

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\ProductPagesLicensing\Api\Activator;
use Inpsyde\ProductPagesLicensing\License;
use function Inpsyde\MultilingualPress\printNonceField;

class LicenseSettingsTabView implements SettingsPageView
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
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function render()
    {
        // phpcs:enable

        $licenseOption = get_network_option(0, 'multilingualpress_license');
        $licenseOption['api_key'] = isset($licenseOption['api_key']) ? $licenseOption['api_key'] : '';
        $licenseOption['license_product_id'] = isset($licenseOption['license_product_id'])
            ? $licenseOption['license_product_id']
            : '';
        $licenseOption['instance_key'] = isset($licenseOption['instance_key'])
            ? $licenseOption['instance_key']
            : wp_generate_password(12, false);

        $status = $this->activator->status(
            new License(
                $licenseOption['license_product_id'],
                $licenseOption['api_key'],
                $licenseOption['instance_key'],
                isset($licenseOption['status']) ? $licenseOption['status'] : 'inactive'
            )
        );

        $statusCheck = isset($status['status']) ? $status['status'] : 'inactive';
        $licenseOption['status'] = $statusCheck;
        update_network_option(0, 'multilingualpress_license', $licenseOption);

        printNonceField($this->nonce);
        ?>
        <table class="form-table widefat mlp-settings-table mlp-license-settings">
            <tr>
                <td><?= wp_kses_post(__('This version of MultilingualPress has a new licensing system that requires a Master Api Key and a Product ID in order to be activated. These values are available in your <a href="https://multilingualpress.org/my-account/" target="_blank">My Account</a> section. Further information is available <a href="https://multilingualpress.org/docs/multilingualpress-license-update/" target="_blank">here</a>.', 'multilingualpress')) ?></td>
            </tr>
        </table>
        <table class="form-table widefat mlp-settings-table mlp-license-settings">
            <tbody class="table-body-block">
            <tr>
                <th scope="row">
                    <?php esc_html_e('Status', 'multilingualpress'); ?>
                </th>
                <td class="mlp-licence-api-key-status"
                    data-status="<?php echo esc_attr(sanitize_key($statusCheck)) ?>"
                >
                    <?php echo esc_html(ucfirst($statusCheck)); ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?php $this->inputNameAttr('key') ?>">
                        <?php esc_html_e('Master API Key', 'multilingualpress'); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                           id="<?php $this->inputNameAttr('key') ?>"
                           name="<?php $this->inputNameAttr('key') ?>"
                           value="<?php echo esc_attr($licenseOption['api_key']); ?>"
                    />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?php $this->inputNameAttr('license_product_id') ?>">
                        <?php esc_html_e('Product ID', 'multilingualpress'); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                           id="<?php $this->inputNameAttr('license_product_id') ?>"
                           name="<?php $this->inputNameAttr('license_product_id') ?>"
                           value="<?php echo esc_attr($licenseOption['license_product_id']); ?>"
                    />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?php $this->inputNameAttr('deactivate') ?>">
                        <?php esc_html_e('Deactivate license', 'multilingualpress') ?>
                    </label>
                </th>
                <td>
                    <label>
                        <input type="checkbox"
                               id="<?php $this->inputNameAttr('deactivate') ?>"
                               name="<?php $this->inputNameAttr('deactivate') ?>"
                               value="1"
                        />
                    </label>
                </td>
            </tr>
            </tbody>
        </table>

        <input type="hidden"
               name="<?php $this->inputNameAttr('instance') ?>"
               value="<?php echo esc_attr($licenseOption['instance_key']); ?>"
        />
        <?php
    }

    /**
     * @param string $attr
     * @return void Echo the attribute
     */
    private function inputNameAttr(string $attr)
    {
        $settingKey = 'multilingualpress_license';
        $productId = 'license_product_id';
        $apiKey = 'api_key';
        $instanceKey = 'instance_key';

        switch ($attr) {
            case 'license_product_id':
                $attrValue = "{$settingKey}[{$productId}]";
                break;
            case 'key':
                $attrValue = "{$settingKey}[{$apiKey}]";
                break;
            case 'instance':
                $attrValue = "{$settingKey}[{$instanceKey}]";
                break;
            case 'deactivate':
                $attrValue = "{$settingKey}[deactivate]";
                break;
            default:
                $attrValue = '';
        }

        echo esc_attr($attrValue);
    }
}
