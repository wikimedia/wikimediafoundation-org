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

namespace Inpsyde\MultilingualPress\NavMenu;

use Inpsyde\MultilingualPress\Cache\NavMenuItemsSerializer;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Api\Translations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;
use Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Server;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;
use Inpsyde\MultilingualPress\Framework\WordpressContext;
use Inpsyde\MultilingualPress\NavMenu\CopyNavMenu\Ajax\CopyNavMenuSettingsView;
use Inpsyde\MultilingualPress\NavMenu\CopyNavMenu\CopyNavMenu;

use function Inpsyde\MultilingualPress\isWpDebugMode;
use function Inpsyde\MultilingualPress\wpHookProxy;

final class ServiceProvider implements BootstrappableServiceProvider, IntegrationServiceProvider
{
    const NONCE_ACTION = 'add_languages_to_nav_menu';
    const NONCE_COPY_NAV_MENU_ACTION = 'copy_nav_menu';

    /**
     * @inheritdoc
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $container->addService(
            AjaxHandler::class,
            static function (Container $container): AjaxHandler {
                return new AjaxHandler(
                    $container[NonceFactory::class]->create([self::NONCE_ACTION]),
                    $container[ItemRepository::class],
                    $container[ServerRequest::class]
                );
            }
        );

        $container->addService(
            CopyNavMenuSettingsView::class,
            static function (Container $container): CopyNavMenuSettingsView {
                return new CopyNavMenuSettingsView(
                    $container[NonceFactory::class]->create([self::NONCE_COPY_NAV_MENU_ACTION])
                );
            }
        );

        $container->addService(
            ItemDeletor::class,
            static function (Container $container): ItemDeletor {
                return new ItemDeletor($container[\wpdb::class]);
            }
        );

        $container->addService(
            ItemFilter::class,
            static function (Container $container): ItemFilter {
                return new ItemFilter(
                    $container[Translations::class],
                    $container[ItemRepository::class],
                    new Facade($container[Server::class], ItemFilter::class),
                    $container[CacheSettingsRepository::class]
                );
            }
        );

        $container->addService(
            LanguagesMetaboxView::class,
            static function (Container $container): LanguagesMetaboxView {
                $nonce = $container[NonceFactory::class]->create([self::NONCE_ACTION]);
                return new LanguagesMetaboxView($nonce);
            }
        );

        $container->addService(
            CopyNavMenu::class,
            static function (Container $container): CopyNavMenu {
                return new CopyNavMenu(
                    $container[NonceFactory::class]->create([self::NONCE_COPY_NAV_MENU_ACTION]),
                    $container[ServerRequest::class]
                );
            }
        );

        $container->share(
            ItemRepository::class,
            static function (): ItemRepository {
                return new ItemRepository();
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        if (!is_admin()) {
            $this->integrateCache($container);
        }
    }

    /**
     * @inheritdoc
     */
    public function bootstrap(Container $container)
    {
        $this->handleDeleteSiteAction($container[ItemDeletor::class]);

        if (is_admin()) {
            $this->bootstrapAdmin($container);

            return;
        }

        $itemFilter = $container[ItemFilter::class];
        add_filter('wp_nav_menu_objects', wpHookProxy([$itemFilter, 'filterItems']), PHP_INT_MAX);
    }

    /**
     * @param Container $container
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    private function bootstrapAdmin(Container $container)
    {
        // phpcs:enable
        $assetManager = $container[AssetManager::class];
        $metaboxView = $container[LanguagesMetaboxView::class];
        $nonce = $container[NonceFactory::class]->create([self::NONCE_ACTION]);
        $wordpressContext = $container[WordpressContext::class];
        $copyNavMenu = $container[CopyNavMenu::class];

        add_action(
            'load-nav-menus.php',
            static function () use ($assetManager, $nonce) {
                try {
                    $assetManager->enqueueScriptWithData(
                        'multilingualpress-admin',
                        'mlpNavMenusSettings',
                        [
                            'action' => AjaxHandler::ACTION,
                            'metaBoxId' => 'mlp-languages',
                            'nonce' => (string)$nonce,
                            'nonceName' => $nonce->action(),
                        ]
                    );
                    $assetManager->enqueueStyle('multilingualpress-admin');
                } catch (AssetException $exc) {
                    if (isWpDebugMode()) {
                        throw $exc;
                    }
                }
            }
        );

        add_action(
            'admin_init',
            static function () use ($metaboxView, $wordpressContext, $copyNavMenu) {
                if ($wordpressContext->isType(WordPressContext::TYPE_CUSTOMIZER)) {
                    return;
                }

                add_meta_box(
                    'mlp-languages',
                    esc_html__('Languages', 'multilingualpress'),
                    [$metaboxView, 'render'],
                    'nav-menus',
                    'side',
                    'low'
                );

                $copyNavMenu->handleCopyNavMenu();
            }
        );

        add_action(
            'wp_ajax_' . AjaxHandler::ACTION,
            [$container[AjaxHandler::class], 'handle']
        );

        add_action(
            'wp_ajax_' . CopyNavMenuSettingsView::ACTION,
            [$container[CopyNavMenuSettingsView::class], 'handle']
        );

        $itemRepository = $container[ItemRepository::class];

        add_filter(
            'wp_setup_nav_menu_item',
            static function ($item) use ($itemRepository) {
                if ($itemRepository->siteIdOfMenuItem((int)($item->ID ?? 0))) {
                    $item->type_label = esc_html__(
                        'Language',
                        'multilingualpress'
                    );
                }

                return $item;
            }
        );
    }

    /**
     * @param Container $container
     */
    private function integrateCache(Container $container)
    {
        $itemFilter = $container[ItemFilter::class];
        $itemFilterCacheLogic =
            (new ItemLogic(ItemFilter::class, ItemFilter::ITEMS_FILTER_CACHE_KEY))
                ->generateKeyWith(
                    static function (string $key, array $postArrays): string {
                        $url = parse_url(add_query_arg([]), PHP_URL_PATH);
                        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
                        $key .= substr(md5(serialize(array_keys($postArrays)) . $url), -12, 10);
                        return $key;
                    }
                )
                ->updateWith(
                    static function (array $postArrays) use ($itemFilter): array {

                        $postArrays = array_values($postArrays);
                        $serializer = NavMenuItemsSerializer::fromSerialized(...$postArrays);
                        $filtered = $itemFilter->filterItems($serializer->unserialize());

                        return NavMenuItemsSerializer::fromWpPostItems(...$filtered)->serialize();
                    }
                );

        $container[Server::class]->register($itemFilterCacheLogic);
    }

    /**
     * @param ItemDeletor $itemDeletor
     * @return void
     * @throws \Throwable
     */
    private function handleDeleteSiteAction(ItemDeletor $itemDeletor)
    {
        global $wp_version;
        if (version_compare($wp_version, '5.1', '<')) {
            add_action('delete_blog', wpHookProxy(static function (int $siteId) use ($itemDeletor) {
                $site = get_site($siteId);
                $site and $itemDeletor->deleteItemsForDeletedSite($site);
            }));
            return;
        }

        add_action('wp_uninitialize_site', wpHookProxy([$itemDeletor, 'deleteItemsForDeletedSite']));
    }
}
