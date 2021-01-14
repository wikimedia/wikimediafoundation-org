<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress\WooCommerce\Brands;

use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Translator\TermTranslator;

class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'multilingualpress-woocommerce-brands';

    /**
     * Registers the module at the module manager.
     *
     * @param ModuleManager $moduleManager
     * @return bool
     * @throws \Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => "{$this->description()} {$this->disabledDescription()}",
                    'name' => __('WooCommerce Brands', 'multilingualpress-woocommerce-brands'),
                    'active' => true,
                    'disabled' => !$this->isWooCommerceBrandsActive(),
                ]
            )
        );
    }

    /**
     * Performs various tasks on module activation.
     *
     * @param Container $container
     */
    public function activateModule(Container $container)
    {
        $termTranslator = $container[TermTranslator::class];

        $termTranslator->registerBaseStructureCallback(
            'product_brand',
            function () {
                $brandBase = (string)get_option('woocommerce_brand_permalink', 'brand');
                return ($this->permalinksStructure() && $brandBase ?: '');
            }
        );
    }

    /**
     * Registers the provided services on the given container.
     *
     * @param Container $container
     */
    public function register(Container $container) {}

    /**
     * Get the permalinks structure by WordPress option
     *
     * @return string
     */
    private function permalinksStructure(): string
    {
        return (string)get_option('permalink_structure', '');
    }

    /**
     * @return bool
     */
    private function isWooCommerceBrandsActive(): bool
    {
        return class_exists('WC_Brands');
    }

    /**
     * @return mixed
     */
    private function description()
    {
        return __(
            'Enable WooCommerce Brands Support for MultilingualPress.',
            'multilingualpress-woocommerce-brands'
        );
    }

    /**
     * @return string
     */
    private function disabledDescription()
    {
        if (!$this->isWooCommerceBrandsActive()) {
            return __(
                'The module can be activated only if WooCommerce Brands is active at least in the main site.',
                'multilingualpress-woocommerce-brands'
            );
        }

        return '';
    }
}
