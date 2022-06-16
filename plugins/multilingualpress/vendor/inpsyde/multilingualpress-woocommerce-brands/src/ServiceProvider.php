<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress\WooCommerce\Brands;

use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Translator\TermTranslator;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;

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
                    'name' => __('WooCommerce Brands', 'multilingualpress'),
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
            function (): string {
                $brandBase = (string)get_option('woocommerce_brand_permalink', 'brand');
                return $this->permalinksStructure() && $brandBase ? $brandBase : '';
            }
        );
    }

    /**
     * Registers the provided services on the given container.
     *
     * @param Container $container
     */
    public function register(Container $container)
    {
        $moduleManager = $container[ModuleManager::class];

        if (!$moduleManager->isModuleActive(self::MODULE_ID)) {
            $this->whenModuleIsInactive($container);
        }
    }

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
        return function_exists('wc_brands_init');
    }

    /**
     * @return mixed
     */
    private function description()
    {
        return __(
            'Enable WooCommerce Brands Support for MultilingualPress.',
            'multilingualpress'
        );
    }

    /**
     * @return string
     */
    private function disabledDescription(): string
    {
        if (!$this->isWooCommerceBrandsActive()) {
            return __(
                'The module can be activated only if WooCommerce Brands is active at least in the main site.',
                'multilingualpress'
            );
        }

        return '';
    }

    /**
     * Perform necessary actions when the module is not active
     *
     * @param Container $container
     */
    protected function whenModuleIsInactive(Container $container)
    {
        $taxonomyRepository = $container[TaxonomyRepository::class];
        add_filter(
            $taxonomyRepository::FILTER_SUPPORTED_TAXONOMIES,
            static function (array $supported): array {
                unset($supported['product_brand']);
                return $supported;
            }
        );
        add_filter(
            $taxonomyRepository::FILTER_ALL_AVAILABLE_TAXONOMIES,
            static function (array $taxonomies): array {
                unset($taxonomies['product_brand']);
                return $taxonomies;
            }
        );
    }
}
