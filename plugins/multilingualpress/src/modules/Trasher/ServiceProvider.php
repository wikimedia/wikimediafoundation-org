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

namespace Inpsyde\MultilingualPress\Module\Trasher;

use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\Container;

use function Inpsyde\MultilingualPress\wpHookProxy;

final class ServiceProvider implements ModuleServiceProvider
{
    const NONCE_ACTION = 'save_trasher_setting';

    const MODULE_ID = 'trasher';

    const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'trasher_asset_factory';

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $container->share(
            TrasherSettingRepository::class,
            static function (): TrasherSettingRepository {
                return new TrasherSettingRepository();
            }
        );

        $container->addService(
            Trasher::class,
            static function (Container $container): Trasher {
                return new Trasher(
                    $container[TrasherSettingRepository::class],
                    $container[ContentRelations::class],
                    $container[ActivePostTypes::class]
                );
            }
        );

        $container->addService(
            TrasherSettingUpdater::class,
            static function (Container $container): TrasherSettingUpdater {
                return new TrasherSettingUpdater(
                    $container[TrasherSettingRepository::class],
                    $container[ContentRelations::class],
                    $container[ServerRequest::class],
                    $container[NonceFactory::class]->create([self::NONCE_ACTION]),
                    $container[ActivePostTypes::class]
                );
            }
        );

        $container->addService(
            TrasherSettingView::class,
            static function (Container $container): TrasherSettingView {
                return new TrasherSettingView(
                    $container[TrasherSettingRepository::class],
                    $container[NonceFactory::class]->create([self::NONCE_ACTION]),
                    $container[ActivePostTypes::class]
                );
            }
        );

        $container->share(
            self::MODULE_ASSETS_FACTORY_SERVICE_NAME,
            static function (Container $container): AssetFactory {
                $pluginProperties = $container[PluginProperties::class];

                $locations = new Locations();
                $locations
                    ->add(
                        'css',
                        $pluginProperties->dirPath() . 'src/modules/Trasher/public/css',
                        $pluginProperties->dirUrl() . 'src/modules/Trasher/public/css'
                    )
                    ->add(
                        'js',
                        $pluginProperties->dirPath() . 'src/modules/Trasher/public/js',
                        $pluginProperties->dirUrl() . 'src/modules/Trasher/public/js'
                    );

                return new AssetFactory($locations);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => __(
                        'Enable the Thrash checkbox on post/page edit page: this allows you to send all the translations to trash when the source post/page is trashed.',
                        'multilingualpress'
                    ),
                    'name' => __('Trasher', 'multilingualpress'),
                    'active' => false,
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function activateModule(Container $container)
    {
        $trasher = $container[Trasher::class];
        $trasherSettingUpdater = $container[TrasherSettingUpdater::class];
        $trasherSettingView = $container[TrasherSettingView::class];

        add_action('post_submitbox_misc_actions', wpHookProxy([$trasherSettingView, 'render']));
        add_action('save_post', wpHookProxy([$trasherSettingUpdater, 'update']), 10, 2);
        add_action('wp_trash_post', wpHookProxy([$trasher, 'trashRelatedPosts']));

        $assetManager = $container[AssetManager::class];
        /** @var AssetFactory $assetFactory */
        $assetFactory = $container[self::MODULE_ASSETS_FACTORY_SERVICE_NAME];

        $assetManager
            ->registerScript(
                $assetFactory->createInternalScript(
                    'multilingualpress-trasher',
                    'admin.min.js',
                    [
                        'wp-i18n',
                        'wp-element',
                        'wp-editor',
                        'wp-plugins',
                        'wp-edit-post',
                    ]
                )
            )
            ->enqueueScript(
                'multilingualpress-trasher',
                true,
                'enqueue_block_editor_assets'
            );

        add_filter(
            ActivePostTypes::FILTER_ACTIVE_POST_TYPES,
            function ($postTypes) use ($trasherSettingUpdater) {
                foreach ($postTypes as $postType) {
                    $this->addRestInsertAction($postType, $trasherSettingUpdater);
                }
                return $postTypes;
            }
        );

        add_action('admin_init', static function () {
            register_meta('post', '_trash_the_other_posts', [
                'show_in_rest' => true,
            ]);
        });
    }

    /**
     * @param string $postType
     * @param TrasherSettingUpdater $trasherSettingUpdater
     * @return void
     */
    private function addRestInsertAction(
        string $postType,
        TrasherSettingUpdater $trasherSettingUpdater
    ) {

        if (post_type_supports($postType, 'custom-fields')) {
            add_action(
                "rest_insert_{$postType}",
                [$trasherSettingUpdater, 'updateFromRestApi'],
                10,
                2
            );
        }
    }
}
