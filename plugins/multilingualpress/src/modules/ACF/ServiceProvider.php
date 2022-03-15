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

namespace Inpsyde\MultilingualPress\Module\ACF;

use Inpsyde\MultilingualPress\Attachment\Copier;
use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed;
use Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer;
use Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Post\Metabox;
use Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper;

/**
 * Class ServiceProvider
 */
class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'acf';

    /**
     * @inheritDoc
     * @param ModuleManager $moduleManager
     * @return bool
     * @throws ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        $disabledDescription = '';
        $description = __(
            'Enable ACF Support for MultilingualPress.',
            'multilingualpress'
        );

        if (!$this->isACFActive()) {
            $disabledDescription = __(
                'The module can be activated only if ACF plugin is active at least in the main site.',
                'multilingualpress'
            );
        }

        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => "{$description} {$disabledDescription}",
                    'name' => __('ACF', 'multilingualpress'),
                    'active' => true,
                    'disabled' => !$this->isACFActive(),
                ]
            )
        );
    }

    /**
     * @inheritdoc
     *
     * @param Container $container
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     */
    public function register(Container $container)
    {
        if (!$this->isACFActive()) {
            return;
        }

        $container->addService(
            FieldCopier::class,
            static function () use ($container): FieldCopier {
                return new FieldCopier($container[Copier::class]);
            }
        );

        $this->disableSettingsForAcfEntities();
    }

    /**
     * @inheritdoc
     */
    public function activateModule(Container $container)
    {
        if (!$this->isACFActive()) {
            return;
        }

        $this->activateMetaboxes();
        $this->enableCopyACFFields($container);
    }

    /**
     * Setup Metabox Fields
     */
    private function activateMetaboxes()
    {
        add_filter(
            Metabox::HOOK_PREFIX . 'tabs',
            static function (array $tabs): array {
                $acfMetaboxFields = new MetaboxFields();
                return array_merge($tabs, $acfMetaboxFields->allFieldsTabs());
            },
            10,
            2
        );
    }

    /**
     * Enable ACF fields copying functionality
     *
     * @param Container $container
     */
    private function enableCopyACFFields(Container $container)
    {
        $fieldCopier = $container[FieldCopier::class];
        add_filter(
            PostRelationSaveHelper::FILTER_SYNC_KEYS,
            [$fieldCopier, 'handleCopyACFFields'],
            10,
            3
        );
    }

    /**
     * @return bool
     */
    private function isACFActive(): bool
    {
        return \class_exists('ACF');
    }

    /**
     * Disable MLP settings for ACF custom post type.
     *
     * Regardless of whether the ACF module is active, ACF custom post type settings should be removed
     * from admin area, cause they are not translatable. The custom post type is called "Field Groups"
     */
    protected function disableSettingsForAcfEntities()
    {
        $filters = [
            PostTypeRepository::FILTER_ALL_AVAILABLE_POST_TYPES,
            PostTypeRepository::FILTER_SUPPORTED_POST_TYPES,
        ];
        foreach ($filters as $filter) {
            add_filter(
                $filter,
                static function (array $postTypes): array {
                    unset($postTypes['acf-field-group']);
                    return $postTypes;
                }
            );
        }
    }
}
