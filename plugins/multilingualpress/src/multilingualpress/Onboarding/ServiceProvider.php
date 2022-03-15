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

namespace Inpsyde\MultilingualPress\Onboarding;

use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;
use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository as PointersRepository;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Core\Admin\Pointers\Pointers;

/**
 * Service provider for Onboarding
 */
class ServiceProvider implements IntegrationServiceProvider, BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container->addService(
            State::class,
            static function (): State {
                return new State();
            }
        );

        $container->addService(
            Notice::class,
            static function (Container $container): Notice {
                return new Notice($container[State::class]);
            }
        );

        $container->addService(
            Onboarding::class,
            static function (Container $container): Onboarding {
                return new Onboarding(
                    $container[AssetManager::class],
                    $container[SiteRelations::class],
                    $container[ServerRequest::class],
                    $container[State::class],
                    $container[Notice::class]
                );
            }
        );

        $container->addService(
            PointersRepository::class,
            static function (): PointersRepository {
                return new PointersRepository();
            }
        );

        $container->addService(
            Pointers::class,
            static function (Container $container): Pointers {
                return new Pointers(
                    $container[ServerRequest::class],
                    $container[PointersRepository::class],
                    $container[AssetManager::class]
                );
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        $onboarding = $container[Onboarding::class];
        add_action('current_screen', static function () use ($onboarding) {
            $currentScreen = get_current_screen();
            $currentScreen and $onboarding->messages();
            $currentScreen and $onboarding->handleLanguageSettings();
        });

        $this->registerPointersForScreen($container);
        $this->registerPointersActionForScreen($container);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap(Container $container)
    {
        $this->registerAssets($container);

        add_action('admin_enqueue_scripts', [$container[Pointers::class], 'createPointers']);

        $this->dismissPointersForNewUsers();
        $this->dismissPointersOnAjaxCalls($container);

        $this->handleDismissOnboardingMessage($container);
    }

    /**
     * @param Container $container
     * @return void
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    private function registerPointersForScreen(Container $container)
    {
        // phpcs:enable

        $pointersRepository = $container[PointersRepository::class];

        $pointersRepository
            ->registerForScreen(
                'sites_page_multilingualpress-site-settings-network',
                'multilingualpress_edit_site_language',
                '#mlp-site-language',
                'multilingualpress_edit_relationships_languages',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Language', 'pointers', 'multilingualpress'),
                        _x('Select the language of the site.', 'pointers', 'multilingualpress')
                    ),
                    'position' => [
                        'edge' => 'top',
                        'align' => 'left',
                    ],
                ]
            )->registerForScreen(
                'sites_page_multilingualpress-site-settings-network',
                'multilingualpress_edit_relationships_languages',
                '.mlp-relationships-languages',
                'multilingualpress_edit_site',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Site Relationships', 'pointers', 'multilingualpress'),
                        _x(
                            'Set up site relationships to existing sites in the network.',
                            'pointers',
                            'multilingualpress'
                        )
                    ),
                    'position' => [
                        'edge' => 'bottom',
                        'align' => 'left',
                    ],
                ]
            )->registerForScreen(
                'sites_page_multilingualpress-site-settings-network',
                'multilingualpress_edit_site',
                '#submit',
                '',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Save Changes', 'pointers', 'multilingualpress'),
                        _x('Finally, save to apply the changes.', 'pointers', 'multilingualpress')
                    ),
                    'position' => [
                        'edge' => 'bottom',
                        'align' => 'left',
                    ],
                ]
            );

        $pointersRepository
            ->registerForScreen(
                'site-new-network',
                'multilingualpress_new_site_language',
                '#mlp-site-language',
                'multilingualpress_new_relationships_languages',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Language', 'pointers', 'multilingualpress'),
                        _x(
                            'After filling the above WordPress site fields, select the language of the site.',
                            'pointers',
                            'multilingualpress'
                        )
                    ),
                    'position' => [
                        'edge' => 'top',
                        'align' => 'left',
                    ],
                ]
            )->registerForScreen(
                'site-new-network',
                'multilingualpress_new_relationships_languages',
                '.mlp-relationships-languages',
                'multilingualpress_based_on_site',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Site Relationships', 'pointers', 'multilingualpress'),
                        _x(
                            'Set up site relationships to existing sites in the network.',
                            'pointers',
                            'multilingualpress'
                        )
                    ),
                    'position' => [
                        'edge' => 'bottom',
                        'align' => 'left',
                    ],
                ]
            )->registerForScreen(
                'site-new-network',
                'multilingualpress_based_on_site',
                '#mlp-base-site-id',
                'multilingualpress_add_site',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Based on site', 'pointers', 'multilingualpress'),
                        _x(
                            'Select a site to copy all its contents to the new site.',
                            'pointers',
                            'multilingualpress'
                        )
                    ),
                    'position' => [
                        'edge' => 'bottom',
                        'align' => 'left',
                    ],
                ]
            )->registerForScreen(
                'site-new-network',
                'multilingualpress_add_site',
                '#add-site',
                '',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Save Changes', 'pointers', 'multilingualpress'),
                        _x(
                            'Finally, save to apply the changes.',
                            'pointers',
                            'multilingualpress'
                        )
                    ),
                    'position' => [
                        'edge' => 'bottom',
                        'align' => 'left',
                    ],
                ]
            );

        $pointersRepository
            ->registerForScreen(
                'toplevel_page_multilingualpress-network',
                'multilingualpress_settings_dynamic_permalinks',
                '#mlp-post-type-page-permalinks',
                '',
                [],
                [
                    'content' => sprintf(
                        '<h3>%1$s</h3><p>%2$s</p>',
                        _x('Use Dynamic Permalinks', 'pointers', 'multilingualpress'),
                        _x(
                            'If the post type can not translate the URL, you can activate dynamic permalinks (which is a plain URL) as a workaround to solve the problem.',
                            'pointers',
                            'multilingualpress'
                        )
                    ),
                    'position' => [
                        'edge' => 'top',
                        'align' => 'left',
                    ],
                ]
            );
    }

    /**
     * @param Container $container
     * @return void
     */
    private function registerPointersActionForScreen(Container $container)
    {
        $pointersRepository = $container[PointersRepository::class];

        $pointersRepository->registerActionForScreen(
            'sites_page_multilingualpress-site-settings-network',
            'edit_site_dismiss'
        );

        $pointersRepository->registerActionForScreen('site-new-network', 'new_site_dismiss');

        $pointersRepository->registerActionForScreen(
            'toplevel_page_multilingualpress-network',
            'settings_dynamic_permalinks'
        );
    }

    /**
     * @param Container $container
     * @return void
     */
    private function registerAssets(Container $container)
    {
        $assetFactory = $container[AssetFactory::class];
        $container[AssetManager::class]
            ->registerScript(
                $assetFactory->createInternalScript(
                    'onboarding',
                    'onboarding.min.js'
                )
            )->registerScript(
                $assetFactory->createInternalScript(
                    'pointers',
                    'pointers.js'
                )
            );
    }

    /**
     * @param Container $container
     * @return void
     */
    private function handleDismissOnboardingMessage(Container $container)
    {
        add_action(
            'wp_ajax_onboarding_plugin',
            [$container[Onboarding::class], 'handleAjaxDismissOnboardingMessage']
        );

        add_action('admin_init', [$container[Onboarding::class], 'handleDismissOnboardingMessage']);
    }

    /**
     * @return void
     */
    private function dismissPointersForNewUsers()
    {
        add_action('user_register', static function ($userId) {

            $dismissedPointers = explode(
                ',',
                (string)get_user_meta(get_current_user_id(), Pointers::USER_META_KEY, true)
            );
            foreach ($dismissedPointers as $pointer) {
                add_user_meta($userId, Pointers::USER_META_KEY, $pointer);
            }
        });
    }

    /**
     * @param Container $container
     */
    private function dismissPointersOnAjaxCalls(Container $container)
    {
        add_action('wp_ajax_edit_site_dismiss', [$container[Pointers::class], 'dismiss']);
        add_action('wp_ajax_new_site_dismiss', [$container[Pointers::class], 'dismiss']);
        add_action('wp_ajax_settings_dynamic_permalinks', [$container[Pointers::class], 'dismiss']);
    }
}
