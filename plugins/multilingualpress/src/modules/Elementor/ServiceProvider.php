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

namespace Inpsyde\MultilingualPress\Module\Elementor;

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
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Module\Redirect\RedirectRequestChecker;

use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Class ServiceProvider
 */
class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'elementor';
    const ELEMENTOR_POSTMETA_METAKEYS = 'elementor.postmeta.metakeys';
    const ELEMENTOR_ENTITIES_TO_REMOVE_SUPPORT = 'elementor.entities.slugs';
    const FILTERS_NEEDED_TO_REMOVE_ENTITIES_SUPPORT = 'filters.needed.to.remove.entities.support';

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
            'Enable Elementor Support for MultilingualPress.',
            'multilingualpress'
        );

        if (!$this->isElementorActive()) {
            $disabledDescription = __(
                'The module can be activated only if Elementor is active at least in the main site.',
                'multilingualpress'
            );
        }

        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => "{$description} {$disabledDescription}",
                    'name' => __('Elementor', 'multilingualpress'),
                    'active' => true,
                    'disabled' => !$this->isElementorActive(),
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

        if (!$this->isElementorActive()) {
            return;
        }

        /**
         * Config for post metas which needs to be copied
         */
        $container->share(
            self::ELEMENTOR_POSTMETA_METAKEYS,
            static function (): array {
                return [
                    '_elementor_data',
                    '_elementor_controls_usage',
                    '_elementor_css',
                    '_elementor_edit_mode',
                    '_elementor_version',
                ];
            }
        );

        /**
         * Config for elementor entities which should not be supported
         */
        $container->share(
            self::ELEMENTOR_ENTITIES_TO_REMOVE_SUPPORT,
            static function (): array {
                return ['elementor_library_category', 'elementor_library', 'e-landing-page'];
            }
        );

        /**
         * Config for filters which need to be used to remove the unnecessary entity support
         */
        $container->share(
            self::FILTERS_NEEDED_TO_REMOVE_ENTITIES_SUPPORT,
            static function () use ($container): array {
                $taxonomyRepository = $container[TaxonomyRepository::class];
                $postTypeRepository = $container[PostTypeRepository::class];

                return [
                    $taxonomyRepository::FILTER_SUPPORTED_TAXONOMIES,
                    $taxonomyRepository::FILTER_ALL_AVAILABLE_TAXONOMIES,
                    $postTypeRepository::FILTER_SUPPORTED_POST_TYPES,
                    $postTypeRepository::FILTER_ALL_AVAILABLE_POST_TYPES,
                ];
            }
        );

        $filtersToRemoveEntities = $container[self::FILTERS_NEEDED_TO_REMOVE_ENTITIES_SUPPORT];
        $entitiesToRemoveSupport = $container[self::ELEMENTOR_ENTITIES_TO_REMOVE_SUPPORT];
        foreach ($filtersToRemoveEntities as $filter) {
            $this->filterSupportForEntities($entitiesToRemoveSupport, $filter);
        }

        /**
         * When the post is edited with Elementor and when the MultilingualPress redirection is active,
         * then we should disable the redirection and allow users to edit the post in frontend with Elementor.
         * RedirectRequestChecker::FILTER_REDIRECT filter is used to check if the current request is a redirection.
         *
         * @param bool $isRedirectRequest The param to check if is Redirect Request
         * @return bool false if post is edited with Elementor
         */
        add_filter(RedirectRequestChecker::FILTER_REDIRECT, static function (bool $isRedirectRequest): bool {
            $isElementorPreview = filter_input(INPUT_GET, 'elementor-preview', FILTER_SANITIZE_STRING);
            if ($isElementorPreview) {
                return false;
            }
            return $isRedirectRequest;
        });
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
        if (!$this->isElementorActive()) {
            return;
        }

        $this->handleCopyContentEditions($container);
    }

    /**
     * When "Copy source content" option is checked the method will copy the additional postmeta data
     * Which is needed for elementor to work correctly.
     *
     * @param Container $container
     * @throws LateAccessToNotSharedService
     * @throws NameNotFound
     * @throws Throwable
     */
    protected function handleCopyContentEditions(Container $container)
    {
        $metaToCopy = $container->get(self::ELEMENTOR_POSTMETA_METAKEYS);

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
                    \Elementor\Plugin::$instance->files_manager->clear_cache();
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
            if ($meta === '_elementor_data') {
                $sourceMeta = str_replace('\\', '\\\\', $sourceMeta);
            }
            update_post_meta($remotePostId, $meta, $sourceMeta);
        }
    }

    /**
     * @return bool
     */
    protected function isElementorActive(): bool
    {
        if (!did_action('elementor/loaded')) {
            return false;
        }
        return true;
    }

    /**
     * Elementor Post types and taxonomies doesn't need to be supported
     *
     * @param array $entities for which the support needs to be deleted
     * @param string $filter The filter name which is used to remove support
     *
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    protected function filterSupportForEntities(array $entities, string $filter)
    {
        add_filter(
            $filter,
            static function (array $supported) use ($entities): array {
                foreach ($supported as $entitySlug => $value) {
                    if (!in_array($entitySlug, $entities, true)) {
                        continue;
                    }
                    unset($supported[$entitySlug]);
                }
                return $supported;
            }
        );
    }
}
