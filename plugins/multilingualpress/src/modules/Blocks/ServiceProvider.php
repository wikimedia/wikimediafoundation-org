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

namespace Inpsyde\MultilingualPress\Module\Blocks;

use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeFactory;
use Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeFactoryInterface;
use Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface;
use Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar\BlockTypeRegistrar;
use Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar\BlockTypeRegistrarInterface;
use Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\BlockTypeTemplateRenderer;
use Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface;
use RuntimeException;

class ServiceProvider implements ModuleServiceProvider
{
    public const MODULE_ID = 'blocks';
    public const SCRIPT_NAME_TO_REGISTER_BLOCK_SCRIPTS = 'multilingualpress-blocks';

    /**
     * @inheritdoc
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => __('Enable Gutenberg Blocks Support for MultilingualPress.', 'multilingualpress'),
                    'name' => __('Gutenberg Blocks', 'multilingualpress'),
                    'active' => true,
                    'disabled' => false,
                ]
            )
        );
    }

    /**
     * @inheritdoc
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $container->share(
            BlockTypeTemplateRenderer::class,
            static function (): TemplateRendererInterface {
                return new BlockTypeTemplateRenderer();
            }
        );

        $container->share(
            BlockTypeRegistrar::class,
            static function (): BlockTypeRegistrarInterface {
                return new BlockTypeRegistrar(self::SCRIPT_NAME_TO_REGISTER_BLOCK_SCRIPTS);
            }
        );

        $container->share(
            BlockTypeFactory::class,
            static function (Container $container): BlockTypeFactoryInterface {
                return new BlockTypeFactory(
                    $container->get(BlockTypeTemplateRenderer::class)
                );
            }
        );

        /**
         * Configuration for block types.
         */
        $container->share(
            'multilingualpress.Blocks.BlockTypes',
            static function (): array {
                return [];
            }
        );

        /**
         * Configuration for block type instances.
         *
         * @return array<BlockTypeInterface> The list of block type instances.
         */
        $container->share(
            'multilingualpress.Blocks.BlockTypeInstances',
            static function (Container $container): array {
                $blockTypes = $container->get('multilingualpress.Blocks.BlockTypes');
                $blockTypeFactory = $container->get(BlockTypeFactory::class);
                $blockTypeInstances = [];

                foreach ($blockTypes as $blockType) {
                    $blockTypeInstances[] = $blockTypeFactory->createBlockType($blockType);
                }

                return $blockTypeInstances;
            }
        );

        $container->share(
            'multilingualpress.Blocks.AssetFactory',
            static function (Container $container): AssetFactory {
                $pluginProperties = $container->get(PluginProperties::class);
                $path = 'src/modules/Blocks/public/js';

                $locations = new Locations();
                $locations
                    ->add(
                        'js',
                        "{$pluginProperties->dirPath()}{$path}",
                        "{$pluginProperties->dirUrl()}{$path}"
                    );

                return new AssetFactory($locations);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function activateModule(Container $container)
    {
        $assetManager = $container->get(AssetManager::class);
        $assetFactory = $container->get('multilingualpress.Blocks.AssetFactory');
        $assetManager
            ->registerScript(
                $assetFactory->createInternalScript(
                    self::SCRIPT_NAME_TO_REGISTER_BLOCK_SCRIPTS,
                    'admin.min.js'
                )
            )
            ->enqueueScript(
                self::SCRIPT_NAME_TO_REGISTER_BLOCK_SCRIPTS,
                true,
                'enqueue_block_editor_assets'
            );

        $blockTypes = $container->get('multilingualpress.Blocks.BlockTypeInstances');
        $blockTypeRegistrar = $container->get(BlockTypeRegistrar::class);
        $this->registerBlockTypes($blockTypeRegistrar, $blockTypes);
    }

    /**
     * Registers the given block types.
     *
     * @param BlockTypeRegistrarInterface $blockTypeRegistrar
     * @param BlockTypeInterface[] $blockTypes A list of block types.
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    protected function registerBlockTypes(BlockTypeRegistrarInterface $blockTypeRegistrar, array $blockTypes): void
    {
        // phpcs:enable

        add_action('init', static function () use ($blockTypes, $blockTypeRegistrar) {
            foreach ($blockTypes as $blockType) {
                try {
                    $blockTypeRegistrar->register($blockType);
                } catch (RuntimeException $exception) {
                    throw $exception;
                }
            }
        });
    }
}
