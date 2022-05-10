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

namespace Inpsyde\MultilingualPress\Module\WooCommerce;

use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Attachment;
use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Core\ServiceProvider as CoreServiceProvider;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed;
use Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product;
use Inpsyde\MultilingualPress\TranslationUi\Post;
use Inpsyde\MultilingualPress\Translator\PostTranslator;
use Inpsyde\MultilingualPress\Translator\PostTypeTranslator;
use Inpsyde\MultilingualPress\Translator\TermTranslator;
use wpdb;

use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Class ServiceProvider
 */
class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'woocommerce';

    const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'woocommerce_assets_factory';

    /**
     * @inheritdoc
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        $disabledDescription = '';
        $description = __(
            'Enable WooCommerce Support for MultilingualPress.',
            'multilingualpress'
        );

        if (!$this->isWooCommerceActive()) {
            $disabledDescription = __(
                'The module can be activated only if WooCommerce is active at least in the main site.',
                'multilingualpress'
            );
        }

        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => "{$description} {$disabledDescription}",
                    'name' => __('WooCommerce', 'multilingualpress'),
                    'active' => true,
                    'disabled' => !$this->isWooCommerceActive(),
                ]
            )
        );
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     * @param Container $container
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     */
    public function register(Container $container)
    {
        // phpcs:enable

        if (!$this->isWooCommerceActive()) {
            return;
        }

        $container->addService(
            PermalinkStructure::class,
            static function (): PermalinkStructure {
                return new PermalinkStructure();
            }
        );

        $container->addService(
            AttributesRelationship::class,
            static function () use ($container): AttributesRelationship {
                return new AttributesRelationship(
                    $container[TaxonomyRepository::class],
                    $container[SiteRelations::class],
                    $container[\wpdb::class]
                );
            }
        );

        $container->addService(
            ArchiveProducts::class,
            static function (): ArchiveProducts {
                return new ArchiveProducts();
            }
        );

        $container->addService(
            AvailableTaxonomiesAttributes::class,
            static function (): AvailableTaxonomiesAttributes {
                return new AvailableTaxonomiesAttributes();
            }
        );

        $container->addService(
            AttributeTermTranslateUrl::class,
            static function (Container $container): AttributeTermTranslateUrl {
                return new AttributeTermTranslateUrl(
                    $container[\wpdb::class],
                    $container[UrlFactory::class]
                );
            }
        );

        $container->addService(
            Product\Ajax\Search::class,
            static function (Container $container): Product\Ajax\Search {
                return new Product\Ajax\Search(
                    $container[Post\Ajax\ContextBuilder::class],
                    $container[wpdb::class]
                );
            }
        );
        $container->addValue('product_search_limit', 1000);

        $container->share(
            self::MODULE_ASSETS_FACTORY_SERVICE_NAME,
            static function (Container $container): AssetFactory {
                $pluginProperties = $container[PluginProperties::class];

                $locations = new Locations();
                $locations
                    ->add(
                        'css',
                        $pluginProperties->dirPath() . 'src/modules/WooCommerce/public/css',
                        $pluginProperties->dirUrl() . 'src/modules/WooCommerce/public/css'
                    )
                    ->add(
                        'js',
                        $pluginProperties->dirPath() . 'src/modules/WooCommerce/public/js',
                        $pluginProperties->dirUrl() . 'src/modules/WooCommerce/public/js'
                    );

                return new AssetFactory($locations);
            }
        );

        $this->disableSettingsForWooCommerceEntities($container);

        $moduleManager = $container[ModuleManager::class];
        if (!$moduleManager->isModuleActive(self::MODULE_ID)) {
            $this->removeWooCommerceSupport($container);
        }
    }

    /**
     * @inheritdoc
     * @throws AssetException
     */
    public function activateModule(Container $container)
    {
        if (!$this->isWooCommerceActive()) {
            return;
        }

        $this->bootstrapAssets($container);
        $this->activateBasePermalinkStructures($container);
        $this->activateProductMetaboxes($container);
        $this->removeAttributeTaxonomiesFieldsFromPostMetabox();
        $this->addProductSearchHandler($container);
        $this->postTypeActions($container);
        $this->taxonomyActions($container);
    }

    /**
     * @param Container $container
     */
    private function activateBasePermalinkStructures(Container $container)
    {
        $permalinkStructure = $container[PermalinkStructure::class];
        $postTranslator = $container[PostTranslator::class];
        $termTranslator = $container[TermTranslator::class];

        $postTranslator->registerBaseStructureCallback(
            'product',
            [$permalinkStructure, 'baseforProduct']
        );
        $termTranslator->registerBaseStructureCallback(
            'product_cat',
            [$permalinkStructure, 'forProductCategory']
        );
        $termTranslator->registerBaseStructureCallback(
            'product_tag',
            [$permalinkStructure, 'forProductTag']
        );

        $attributeTaxonomies = wc_get_attribute_taxonomies();
        $attributeTaxonomies and array_walk(
            $attributeTaxonomies,
            static function (\stdClass $attribute) use ($termTranslator, $permalinkStructure) {
                $taxonomySlug = 'pa_' . sanitize_key($attribute->attribute_name);
                $termTranslator->registerBaseStructureCallback(
                    $taxonomySlug,
                    [$permalinkStructure, 'forProductAttribute']
                );
            }
        );
    }

    /**
     * Add Metaboxes for Product
     *
     * @param Container $container
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    private function activateProductMetaboxes(Container $container)
    {
        // phpcs:enable

        $wooCommerceMetaboxFields = new Product\WooCommerceMetaboxFields();
        $panelView = new Product\PanelView(
            new Product\SettingView(
                'general',
                ...$wooCommerceMetaboxFields->generalSettingFields()
            ),
            new Product\SettingView(
                'inventory',
                ...$wooCommerceMetaboxFields->inventorySettingFields()
            ),
            new Product\SettingView(
                'advanced',
                ...$wooCommerceMetaboxFields->advancedSettingFields()
            )
        );
        $metaboxActivator = new ProductMetaboxesBehaviorActivator(
            new Product\MetaboxFields($wooCommerceMetaboxFields),
            $panelView,
            $container[ActivePostTypes::class],
            $container[ContentRelations::class],
            $container[Attachment\Copier::class],
            $container[PersistentAdminNotices::class]
        );

        add_filter(
            Post\Metabox::HOOK_PREFIX . 'tabs',
            [$metaboxActivator, 'setupMetaboxFields'],
            10,
            2
        );

        add_action(
            Product\MetaboxTab::ACTION_BEFORE_METABOX_UI_PANEL,
            [$metaboxActivator, 'renderPanels'],
            20,
            2
        );

        add_action(
            Post\MetaboxAction::ACTION_METABOX_AFTER_RELATE_POSTS,
            [$metaboxActivator, 'saveMetaboxes'],
            10,
            3
        );
    }

    /**
     * Do not add the attributes taxonomies to the list of translatable taxonomies.
     * Them are handled differently within the Product Tab.
     */
    private function removeAttributeTaxonomiesFieldsFromPostMetabox()
    {
        $removeAttributeTaxonomiesNameCallback = static function (array $taxonomiesSlugs): array {
            $attributeTaxonomies = wc_get_attribute_taxonomy_names();
            foreach ($attributeTaxonomies as $taxonomyName) {
                unset($taxonomiesSlugs[$taxonomyName]);
            }

            return $taxonomiesSlugs;
        };

        add_filter(
            Post\Field\TaxonomySlugs::FILTER_FIELD_TAXONOMY_SLUGS,
            $removeAttributeTaxonomiesNameCallback
        );
        add_filter(
            Post\MetaboxAction::FILTER_TAXONOMIES_SLUGS_BEFORE_REMOVE,
            $removeAttributeTaxonomiesNameCallback
        );
        add_filter(
            Post\MetaboxFields::FILTER_TAXONOMIES_AND_TERMS_OF,
            static function ($taxonomiesSlugs) {
                $attributeTaxonomies = wc_get_attribute_taxonomy_names();
                $taxonomiesSlugs = array_filter(
                    $taxonomiesSlugs,
                    static function (string $taxonomySlug) use ($attributeTaxonomies): bool {
                        return !\in_array($taxonomySlug, $attributeTaxonomies, true);
                    }
                );
                return $taxonomiesSlugs;
            }
        );
    }

    /**
     * Setup assets for WooCommerce
     *
     * @param Container $container
     * @throws AssetException
     */
    private function bootstrapAssets(Container $container)
    {
        global $pagenow;

        if (!$this->isEditProductPage($pagenow)) {
            return;
        }

        /** @var AssetFactory $assetFactory */
        $assetFactory = $container[self::MODULE_ASSETS_FACTORY_SERVICE_NAME];
        $container[AssetManager::class]
            ->registerStyle(
                $assetFactory->createInternalStyle(
                    'multilingualpress-woocommerce-admin',
                    'admin.min.css',
                    ['multilingualpress-admin', 'jquery-ui-style']
                )
            )
            ->registerScript(
                $assetFactory->createInternalScript(
                    'multilingualpress-woocommerce-admin',
                    'admin.min.js',
                    ['jquery', 'multilingualpress-admin']
                )
            );

        try {
            $container[AssetManager::class]->enqueueStyle('multilingualpress-woocommerce-admin');
            $container[AssetManager::class]->enqueueScript('multilingualpress-woocommerce-admin');
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }
    }

    /**
     * @return bool
     */
    private function isWooCommerceActive(): bool
    {
        return \function_exists('wc');
    }

    /**
     * Check if the current admin edit page is for post type product
     *
     * @param string $currentPage
     * @return bool
     */
    private function isEditProductPage(string $currentPage): bool
    {
        $postId = (int)filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
        $isAllowedPage = \in_array($currentPage, ['post.php', 'post-new.php'], true);

        $requestPostType = (string)filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
        $requestPostType = $requestPostType ?: get_post_type($postId);

        return $isAllowedPage && 'product' === $requestPostType;
    }

    /**
     * @param Container $container
     * @return void
     */
    protected function addProductSearchHandler(Container $container)
    {
        $sourcePostId = (int)filter_input(INPUT_POST, 'source_post_id', FILTER_SANITIZE_NUMBER_INT);
        if (!$sourcePostId || get_post_type($sourcePostId) !== 'product') {
            return;
        }

        remove_action(
            'wp_ajax_' . Post\Ajax\Search::ACTION,
            [$container[Post\Ajax\Search::class], 'handle']
        );

        $search = $container[Product\Ajax\Search::class];
        $request = $container[ServerRequest::class];

        add_action(
            'wp_ajax_' . Product\Ajax\Search::ACTION,
            static function () use ($search, $request) {
                $search->handle($request);
            }
        );
    }

    /**
     * @param Container $container
     */
    private function postTypeActions(Container $container)
    {
        add_filter(
            PostTypeTranslator::FILTER_POST_TYPE_PERMALINK,
            [$container[ArchiveProducts::class], 'shopArchiveUrl'],
            10,
            3
        );

        add_filter(
            CoreServiceProvider::FILTER_AVAILABLE_POST_TYPE_FOR_SETTINGS,
            static function (array $allAvailablePostTypes): array {
                unset($allAvailablePostTypes['product']);

                return $allAvailablePostTypes;
            }
        );
    }

    /**
     * @param Container $container
     */
    private function taxonomyActions(Container $container)
    {
        $attributeTermTranslateUrl = $container[AttributeTermTranslateUrl::class];
        $attributesRelationship = $container[AttributesRelationship::class];
        $attributeTaxonomies = wc_get_attribute_taxonomy_names();

        add_filter(
            TermTranslator::FILTER_TRANSLATION,
            [$attributeTermTranslateUrl, 'termLinkByTaxonomyId'],
            10,
            4
        );

        add_action('setup_theme', static function () use ($attributeTermTranslateUrl) {
            global $wp_rewrite;
            $attributeTermTranslateUrl->ensureWpRewrite($wp_rewrite);
        });

        foreach ($attributeTaxonomies as $attributeTaxonomy) {
            add_action(
                "{$attributeTaxonomy}_pre_edit_form",
                [$attributesRelationship, 'createAttributeRelation'],
                10,
                2
            );
        }

        $attributesHookNames = ['woocommerce_attribute_added', 'woocommerce_attribute_updated'];
        array_walk($attributesHookNames, static function (string $hookName) use ($attributesRelationship) {
            add_action($hookName, [$attributesRelationship, 'addSupportForAttribute'], 10, 2);
        });
    }

    /**
     * Perform an actions when WooCommerce support is deactivated
     *
     * If Woo support is deactivated we should disable the translation metabox support for
     * Woo entities(Products, all Woo taxonomies) and also we need to disable the
     * Woo post type and taxonomy settings from MLP global settings
     *
     * @param Container $container
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    protected function removeWooCommerceSupport(Container $container)
    {
        // phpcs:enable

        $taxonomyRepository = $container[TaxonomyRepository::class];
        $postTypeRepository = $container[PostTypeRepository::class];
        $filters = [
            $taxonomyRepository::FILTER_SUPPORTED_TAXONOMIES,
            $taxonomyRepository::FILTER_ALL_AVAILABLE_TAXONOMIES,
            $postTypeRepository::FILTER_SUPPORTED_POST_TYPES,
            $postTypeRepository::FILTER_ALL_AVAILABLE_POST_TYPES,
        ];
        $entitiesToRemove = ['product_cat', 'product_tag', 'product'];
        foreach ($filters as $filter) {
            add_filter(
                $filter,
                static function (array $supported) use ($entitiesToRemove): array {
                    foreach ($entitiesToRemove as $entity) {
                        if (!key_exists($entity, $supported)) {
                            continue;
                        }
                        unset($supported[$entity]);
                    }

                    return $supported;
                }
            );
        }
    }

    /**
     * Disable MLP settings for certain WooCommerce entities.
     *
     * Regardless of whether the WooCommerce module is active, some WooCommerce entities settings should be removed
     * from admin area, cause some entities like "Attributes" are supported under the hood when the module is active and
     * some are not translatable at all, such as "Orders".
     * This method is for removing the settings of such entities from admin area.
     *
     * @param Container $container
     */
    protected function disableSettingsForWooCommerceEntities(Container $container)
    {
        add_filter(
            PostTypeRepository::FILTER_PUBLIC_POST_TYPES,
            static function ($allAvailablePostTypes) {
                unset($allAvailablePostTypes['shop_order']);
                return $allAvailablePostTypes;
            }
        );

        add_filter(
            TaxonomyRepository::FILTER_ALL_AVAILABLE_TAXONOMIES,
            [$container[AvailableTaxonomiesAttributes::class], 'removeAttributes']
        );
    }
}
