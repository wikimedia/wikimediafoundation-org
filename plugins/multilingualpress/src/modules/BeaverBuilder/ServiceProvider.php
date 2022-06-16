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

namespace Inpsyde\MultilingualPress\Module\BeaverBuilder;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\LateAccessToNotSharedService;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameNotFound;
use Inpsyde\MultilingualPress\TranslationUi\Post;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Throwable;

use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Class ServiceProvider
 */
class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'beaverbuilder';

    const BEAVERBUILDER_POSTMETA_METAKEYS = 'beaverbuilder.postmeta.metakeys';

    /**
     * @inheritdoc
     *
     * @param ModuleManager $moduleManager
     * @return bool
     * @throws ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        $disabledDescription = '';
        $description = __(
            'Enable Beaver Builder Support for MultilingualPress.',
            'multilingualpress'
        );

        if (!$this->isBeaverBuilderActive()) {
            $disabledDescription = __(
                'The module can be activated only if Beaver Builder is active at least in the main site.',
                'multilingualpress'
            );
        }

        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => "{$description} {$disabledDescription}",
                    'name' => __('Beaver Builder', 'multilingualpress'),
                    'active' => true,
                    'disabled' => !$this->isBeaverBuilderActive(),
                ]
            )
        );
    }

    /**
     * @inheritdoc
     *
     * @param Container $container
     */
    public function register(Container $container)
    {
        // phpcs:enable

        if (!$this->isBeaverBuilderActive()) {
            return;
        }

        /**
         * Config for post metas which needs to be copied
         */
        $container->share(
            self::BEAVERBUILDER_POSTMETA_METAKEYS,
            static function (): array {
                return ['_fl_builder_data', '_fl_builder_data_settings', '_fl_builder_enabled'];
            }
        );
    }

    /**
     * @inheritdoc
     *
     * @param Container $container
     * @throws LateAccessToNotSharedService
     * @throws NameNotFound
     * @throws Throwable
     */
    public function activateModule(Container $container)
    {
        if (!$this->isBeaverBuilderActive()) {
            return;
        }

        $this->handleCopyContentEditions($container);
    }

    /**
     * When "Copy source content" option is checked the method will copy the additional postmeta data
     * Which is needed for Beaver Builder to work correctly.
     *
     * @param Container $container
     * @throws LateAccessToNotSharedService
     * @throws NameNotFound
     * @throws Throwable
     */
    protected function handleCopyContentEditions(Container $container)
    {
        $metaToCopy = $container->get(self::BEAVERBUILDER_POSTMETA_METAKEYS);

        add_action(
            Post\MetaboxAction::ACTION_METABOX_AFTER_RELATE_POSTS,
            wpHookProxy(function (
                Post\RelationshipContext $context,
                Request $request
            ) use ($metaToCopy) {
                $multilingualpress = $request->bodyValue(
                    'multilingualpress',
                    INPUT_POST,
                    FILTER_DEFAULT,
                    FILTER_FORCE_ARRAY
                );

                $remoteSiteId = $context->remoteSiteId();
                $translation = $multilingualpress["site-{$remoteSiteId}"] ?? '';

                if (!empty($translation) && !empty($translation['remote-content-copy'])) {
                    $this->copyMetaData($metaToCopy, $context);
                }
            }),
            10,
            2
        );
    }

    /**
     * Copy Meta data from source post to remote post
     *
     * @param array $data Metadata to be copied
     * @param RelationshipContext $context
     */
    protected function copyMetaData(array $data, RelationshipContext $context)
    {
        $remotePostId = $context->remotePostId();
        $sourcePostId = $context->sourcePostId();
        $sourceSiteId = $context->sourceSiteId();

        foreach ($data as $meta) {
            switch_to_blog($sourceSiteId);
            $sourceMeta = get_post_meta($sourcePostId, $meta, true);
            restore_current_blog();
            if (!$sourceMeta) {
                continue;
            }
            update_post_meta($remotePostId, $meta, $sourceMeta);
        }
    }

    /**
     * @return bool
     */
    protected function isBeaverBuilderActive(): bool
    {
        if (!class_exists('FLBuilderLoader')) {
            return false;
        }
        return true;
    }
}
