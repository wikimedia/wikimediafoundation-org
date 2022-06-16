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

namespace Inpsyde\MultilingualPress\Module\User;

use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxFields;
use Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxView;
use Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxAction;
use Throwable;

use function Inpsyde\MultilingualPress\isWpDebugMode;
use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Module service provider.
 */
class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'user';

    /**
     * @inheritdoc
     * @throws ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => __(
                        'Enable the user information translation settings.',
                        'multilingualpress'
                    ),
                    'name' => __('User Information Translation Settings', 'multilingualpress'),
                    'active' => false,
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container->share(
            MetaboxView::class,
            static function (): MetaboxView {
                return new MetaboxView(
                    new MetaboxFields()
                );
            }
        );

        $container->share(
            MetaboxAction::class,
            static function (): MetaboxAction {
                return new MetaboxAction();
            }
        );

        $container->share(
            MetaValueFilter::class,
            static function (): MetaValueFilter {
                return new MetaValueFilter();
            }
        );

        $container->share(
            MetaboxFields::class,
            static function (): MetaboxFields {
                return new MetaboxFields();
            }
        );
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    public function activateModule(Container $container)
    {
        if (is_network_admin()) {
            return;
        }

        $assetManager = $container[AssetManager::class];
        $this->renderAssets($assetManager);

        $this->metaboxViewActions($container);
        $this->metaboxUpdateActions($container);

        $this->filterUserMetaValues($container);
    }

    /**
     * Will bind the styles for translation metboxes
     *
     * @param AssetManager $assetManager
     */
    protected function renderAssets(AssetManager $assetManager)
    {
        try {
            $assetManager->enqueueStyle('multilingualpress-admin');
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }
    }

    /**
     * Render MultilingualPress custom metaboxes on user profile pages
     *
     * @param Container $container
     * @throws Throwable
     */
    protected function metaboxViewActions(Container $container)
    {
        $metaboxView = $container[MetaboxView::class];

        add_action('show_user_profile', wpHookProxy(static function (\WP_User $user) use ($metaboxView) {
            $metaboxView->render($user);
        }));
        add_action('edit_user_profile', wpHookProxy(static function (\WP_User $user) use ($metaboxView) {
            $metaboxView->render($user);
        }));
    }

    /**
     * When the user profile page is updated we need to save our custom translation meta
     *
     * @param Container $container
     * @throws Throwable
     */
    protected function metaboxUpdateActions(Container $container)
    {
        $request = $container[ServerRequest::class];
        $metaboxAction = $container[MetaboxAction::class];

        add_action(
            'personal_options_update',
            wpHookProxy(static function (int $userId) use ($metaboxAction, $request) {
                $metaboxAction->updateTranslationData($userId, $request);
            })
        );
        add_action(
            'edit_user_profile_update',
            wpHookProxy(static function (int $userId) use ($metaboxAction, $request) {
                $metaboxAction->updateTranslationData($userId, $request);
            })
        );
    }

    /**
     * Filter the frontend values for user meta fields and replace with correct translations
     *
     * @param Container $container
     * @throws Throwable
     */
    protected function filterUserMetaValues(Container $container)
    {
        $metaValueFilter = $container[MetaValueFilter::class];
        $metaboxFields = $container[MetaboxFields::class];

        foreach ($metaboxFields->allFields() as $key => $field) {
            add_filter("the_author_{$key}", wpHookProxy([$metaValueFilter, 'filterMetaValues']), 10, 2);
            add_filter("get_the_author_{$key}", wpHookProxy([$metaValueFilter, 'filterMetaValues']), 10, 2);
        }
    }
}
