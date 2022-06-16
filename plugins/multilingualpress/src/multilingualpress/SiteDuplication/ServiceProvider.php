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

namespace Inpsyde\MultilingualPress\SiteDuplication;

use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Attachment;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsUpdater;
use Inpsyde\MultilingualPress\Database\Table\ContentRelationsTable;
use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Database\Table\RelationshipsTable;
use Inpsyde\MultilingualPress\Database\Table\SiteRelationsTable;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\BasePathAdapter;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Database\TableDuplicator;
use Inpsyde\MultilingualPress\Framework\Database\TableList;
use Inpsyde\MultilingualPress\Framework\Database\TableReplacer;
use Inpsyde\MultilingualPress\Framework\Database\TableStringReplacer;
use Inpsyde\MultilingualPress\Framework\Filesystem;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface;
use Inpsyde\MultilingualPress\Framework\Nonce\Context;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce;
use Inpsyde\MultilingualPress\Framework\Service\Exception\LateAccessToNotSharedService;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameNotFound;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed;
use Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingMultiView;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionView;
use Inpsyde\MultilingualPress\Core\Admin\NewSiteSettings;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Schedule\AjaxScheduleHandler;
use Inpsyde\MultilingualPress\Schedule\Scheduler;
use Inpsyde\MultilingualPress\Schedule\Action\ScheduleActionRequestHandler;
use Inpsyde\MultilingualPress\Schedule\Action\RemoveActionTask;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\AttachmentDuplicatorHandler;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\AttachmentDuplicatorScheduler;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\MaybeScheduleAttachmentDuplication;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\NewSiteScheduleTemplate;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\ScheduleAssetManager;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\RemoveAttachmentIdsTask;
use Inpsyde\MultilingualPress\SiteDuplication\Schedule\ScheduleActionsNames;
use Throwable;
use WP_Site;
use Inpsyde\MultilingualPress\SiteDuplication\Settings\ActivatePluginsSetting;
use Inpsyde\MultilingualPress\SiteDuplication\Settings\BasedOnSiteSetting;
use Inpsyde\MultilingualPress\SiteDuplication\Settings\ConnectContentSetting;
use Inpsyde\MultilingualPress\SiteDuplication\Settings\CopyAttachmentsSetting;
use Inpsyde\MultilingualPress\SiteDuplication\Settings\CopyUsersSetting;
use Inpsyde\MultilingualPress\SiteDuplication\Settings\SearchEngineVisibilitySetting;

use function Inpsyde\MultilingualPress\wpHookProxy;
use function Inpsyde\MultilingualPress\wpVersion;

/**
 * Service provider for all site duplication objects.
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    const SITE_DUPLICATION_SUCCESS_ACTIONS_MESSAGES = 'siteDuplication.successActionsMessages';
    // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
    const SCHEDULE_ACTION_ATTACHMENTS_REMOVER_SERVICE = 'siteDuplication.scheduleActionAttachmentsRemover';
    const SCHEDULE_ACTION_ATTACHMENT_HANDLER_SERVICE = 'siteDuplication.scheduleActionAttachmentHandler';
    const SITE_DUPLICATION_ACTIONS = 'siteDuplication.actionsService';

    const FILTER_SUCCESS_ACTIONS_MESSAGES = 'multilingualpress.filter_success_actions_messages';
    const FILTER_SITE_DUPLICATION_ACTIONS = 'multilingualpress.site_duplication_actions';

    const SCHEDULE_ACTION_ATTACHMENTS_AJAX_HOOK_NAME = 'multilingualpress_site_duplicator_attachments_schedule_action';
    // phpcs:enable
    const SCHEDULE_ACTION_ATTACHMENTS_USER_REQUIRED_CAPABILITY = 'create_sites';
    const SCHEDULE_ACTION_ATTACHMENTS_NONCE_KEY = 'multilingualpress_attachment_duplicator_action';

    const MLP_TABLES = 'multilingualpress.mlpTables';
    const SITE_DUPLICATION_FILTER_MLP_TABLES = 'siteDuplication.filterMlpTables';

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

        $container->addService(
            ActivePlugins::class,
            static function (): ActivePlugins {
                return new ActivePlugins();
            }
        );

        $container->addService(
            Attachment\Duplicator::class,
            static function (Container $container): Attachment\Duplicator {
                return new Attachment\Duplicator(
                    $container[BasePathAdapter::class],
                    $container[Filesystem::class]
                );
            }
        );

        $container->addService(
            ConnectContentSetting::class,
            static function (): ConnectContentSetting {
                return new ConnectContentSetting();
            }
        );

        $container->addService(
            ActivatePluginsSetting::class,
            static function (): ActivatePluginsSetting {
                return new ActivatePluginsSetting();
            }
        );

        $container->addService(
            BasedOnSiteSetting::class,
            function (Container $container): BasedOnSiteSetting {
                return new BasedOnSiteSetting(
                    $container[\wpdb::class],
                    $this->duplicateNonce($container)
                );
            }
        );

        $container->addService(
            CopyAttachmentsSetting::class,
            static function (): CopyAttachmentsSetting {
                return new CopyAttachmentsSetting();
            }
        );

        $container->addService(
            CopyUsersSetting::class,
            static function (): CopyUsersSetting {
                return new CopyUsersSetting();
            }
        );

        $container->addService(
            SearchEngineVisibilitySetting::class,
            static function (): SearchEngineVisibilitySetting {
                return new SearchEngineVisibilitySetting();
            }
        );

        $container->addService(
            SiteDuplicator::class,
            function (Container $container): SiteDuplicator {
                return new SiteDuplicator(
                    $container[\wpdb::class],
                    $container[TableList::class],
                    $container[TableDuplicator::class],
                    $container[TableReplacer::class],
                    $container[ActivePlugins::class],
                    $container[ContentRelations::class],
                    $container[ServerRequest::class],
                    $this->duplicateNonce($container)
                );
            }
        );

        $container->share(self::MLP_TABLES, static function (Container $container): array {
            return [
                $container[ContentRelationsTable::class]->name(),
                $container[LanguagesTable::class]->name(),
                $container[RelationshipsTable::class]->name(),
                $container[SiteRelationsTable::class]->name(),
            ];
        });

        $container->addService(
            SiteScheduleOption::class,
            static function (): SiteScheduleOption {
                return new SiteScheduleOption();
            }
        );

        $container->addService(
            AttachmentDuplicatorScheduler::class,
            static function (Container $container): AttachmentDuplicatorScheduler {
                return new AttachmentDuplicatorScheduler(
                    $container[SiteScheduleOption::class],
                    $container[Attachment\Collection::class],
                    $container[Scheduler::class]
                );
            }
        );

        $container->addService(
            Attachment\DatabaseDataReplacer::class,
            static function (Container $container): Attachment\DatabaseDataReplacer {
                return new Attachment\DatabaseDataReplacer(
                    $container[\wpdb::class],
                    $container[TableStringReplacer::class],
                    $container[BasePathAdapter::class]
                );
            }
        );

        $container->addService(
            AttachmentDuplicatorHandler::class,
            static function (Container $container): AttachmentDuplicatorHandler {
                return new AttachmentDuplicatorHandler(
                    $container[SiteScheduleOption::class],
                    $container[Attachment\Duplicator::class],
                    $container[Attachment\Collection::class],
                    $container[Scheduler::class],
                    $container[Attachment\DatabaseDataReplacer::class]
                );
            }
        );

        $container->addService(
            ScheduleAssetManager::class,
            static function (Container $container): ScheduleAssetManager {
                return new ScheduleAssetManager(
                    $container[SiteScheduleOption::class],
                    $container[AjaxScheduleHandler::class],
                    $container[AssetManager::class],
                    $container[NonceFactory::class]->create(
                        [self::SCHEDULE_ACTION_ATTACHMENTS_NONCE_KEY]
                    )
                );
            }
        );

        $container->addService(
            MaybeScheduleAttachmentDuplication::class,
            static function (Container $container): MaybeScheduleAttachmentDuplication {
                return new MaybeScheduleAttachmentDuplication(
                    $container[ServerRequest::class],
                    $container[AttachmentDuplicatorScheduler::class]
                );
            }
        );

        /* -----------------------------------------------------------------------------
           AttachmentDuplicationProcessAction
           -------------------------------------------------------------------------- */

        $container->addService(
            self::SCHEDULE_ACTION_ATTACHMENTS_REMOVER_SERVICE,
            static function (Container $container): RemoveActionTask {
                return new RemoveActionTask(
                    $container[ServerRequest::class],
                    $container[Scheduler::class],
                    ScheduleAssetManager::NAME_ATTACHMENT_SCHEDULE_ID,
                    AttachmentDuplicatorScheduler::SCHEDULE_HOOK
                );
            }
        );

        $container->addService(
            RemoveAttachmentIdsTask::class,
            static function (Container $container): RemoveAttachmentIdsTask {
                return new RemoveAttachmentIdsTask(
                    $container[ServerRequest::class],
                    $container[SiteScheduleOption::class],
                    ScheduleAssetManager::NAME_SITE_ID
                );
            }
        );

        $container->addService(
            self::SITE_DUPLICATION_ACTIONS,
            static function (Container $container): array {
                return apply_filters(
                    self::FILTER_SITE_DUPLICATION_ACTIONS,
                    [
                        ScheduleActionsNames::STOP_ATTACHMENTS_COPY => [
                            $container[self::SCHEDULE_ACTION_ATTACHMENTS_REMOVER_SERVICE],
                            $container[RemoveAttachmentIdsTask::class],
                        ],
                    ]
                );
            }
        );

        $container->share(
            self::SITE_DUPLICATION_SUCCESS_ACTIONS_MESSAGES,
            static function (): array {
                return apply_filters(
                    self::FILTER_SUCCESS_ACTIONS_MESSAGES,
                    [
                        ScheduleActionsNames::STOP_ATTACHMENTS_COPY => esc_html_x(
                            'Attachments copy has been stopped without errors.',
                            'Site Duplication',
                            'multilingualpress'
                        ),
                    ]
                );
            }
        );

        $container->addService(
            self::SCHEDULE_ACTION_ATTACHMENT_HANDLER_SERVICE,
            static function (Container $container): ScheduleActionRequestHandler {
                return new ScheduleActionRequestHandler(
                    $container[NonceFactory::class]->create(
                        [self::SCHEDULE_ACTION_ATTACHMENTS_NONCE_KEY]
                    ),
                    $container->get(self::SITE_DUPLICATION_ACTIONS),
                    $container->get(MessageFactoryInterface::class),
                    $container->get(Context::class),
                    $container->get(self::SITE_DUPLICATION_SUCCESS_ACTIONS_MESSAGES),
                    self::SCHEDULE_ACTION_ATTACHMENTS_AJAX_HOOK_NAME,
                    self::SCHEDULE_ACTION_ATTACHMENTS_USER_REQUIRED_CAPABILITY
                );
            }
        );
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    public function bootstrap(Container $container)
    {
        $scheduleActionRequestHandler = $container[self::SCHEDULE_ACTION_ATTACHMENT_HANDLER_SERVICE];
        $attachmentDuplicatorHandler = $container[AttachmentDuplicatorHandler::class];
        $settingView = SiteSettingMultiView::fromViewModels(
            [
                $container[BasedOnSiteSetting::class],
                $container[CopyAttachmentsSetting::class],
                $container[ConnectContentSetting::class],
                $container[ActivatePluginsSetting::class],
                $container[CopyUsersSetting::class],
                $container[SearchEngineVisibilitySetting::class],
            ]
        );

        $this->duplicateSiteBackCompactBootstrap($container);
        $this->defineInitialSettingsBackCompactBootstrap($container);
        $this->filterExcludedTables($container);

        add_action(
            SiteSettingsSectionView::ACTION_AFTER . '_' . NewSiteSettings::SECTION_ID,
            [$settingView, 'render']
        );

        add_action('admin_footer', [new NewSiteScheduleTemplate(), 'render']);

        add_action(
            SiteDuplicator::DUPLICATE_ACTION_KEY,
            [
                $container[MaybeScheduleAttachmentDuplication::class],
                'maybeScheduleAttachmentsDuplication',
            ],
            10,
            2
        );

        add_action(
            AttachmentDuplicatorScheduler::SCHEDULE_HOOK,
            [$attachmentDuplicatorHandler, 'handle']
        );

        add_action(
            'wp_ajax_' . self::SCHEDULE_ACTION_ATTACHMENTS_AJAX_HOOK_NAME,
            static function () use ($container, $scheduleActionRequestHandler) {
                $scheduleActionRequestHandler->handle($container[ServerRequest::class]);
            }
        );

        $this->setupScriptsForAdmin($container);
    }

    /**
     * @param Container $container
     */
    protected function setupScriptsForAdmin(Container $container)
    {
        $container[AssetManager::class]
            ->registerScript(
                $container[AssetFactory::class]->createInternalScript(
                    'multilingualpress-site-duplication-admin',
                    'site-duplication-admin.min.js',
                    ['jquery']
                )
            );

        add_action(
            'network_site_new_form',
            [$container[ScheduleAssetManager::class], 'enqueueScript']
        );
    }

    /**
     * @param Container $container
     * @throws Throwable
     */
    protected function duplicateSiteBackCompactBootstrap(Container $container)
    {
        $wpVersion = wpVersion();
        $siteDuplicator = $container->get(SiteDuplicator::class);

        if (version_compare($wpVersion, '5.1', '<')) {
            add_action('wpmu_new_blog', wpHookProxy([$siteDuplicator, 'duplicateSite']), 0);
            return;
        }

        add_action(
            'wp_initialize_site',
            static function (WP_Site $wpSite) use ($siteDuplicator) {
                $siteId = (int)$wpSite->blog_id;
                ($siteId > 0) and $siteDuplicator->duplicateSite($siteId);
            },
            20
        );
    }

    /**
     * @param Container $container
     * @throws LateAccessToNotSharedService
     * @throws NameNotFound
     * @throws Throwable
     */
    protected function defineInitialSettingsBackCompactBootstrap(Container $container)
    {
        $wpVersion = wpVersion();
        $siteSettingsUpdater = $container->get(SiteSettingsUpdater::class);

        if (version_compare($wpVersion, '5.1', '<')) {
            add_action(
                'wpmu_new_blog',
                wpHookProxy([$siteSettingsUpdater, 'defineInitialSettings'])
            );
            return;
        }

        add_action(
            'wp_initialize_site',
            static function (WP_Site $wpSite) use ($siteSettingsUpdater) {
                $siteId = (int)$wpSite->blog_id;
                ($siteId > 0) and $siteSettingsUpdater->defineInitialSettings($siteId);
            },
            20
        );
    }

    /**
     * @param Container $container
     * @return Nonce
     */
    private function duplicateNonce(Container $container): Nonce
    {
        $nonce = $container[NonceFactory::class]->create(['duplicate_site']);
        // When creating a new site, its ID is not yet known, so create a nonce for a fixed site ID 0.
        if ($nonce instanceof SiteAwareNonce) {
            $nonce = $nonce->withSite(0);
        }

        return $nonce;
    }

    /**
     * @param Container $container
     * @throws LateAccessToNotSharedService
     * @throws NameNotFound
     * @throws Throwable
     */
    private function filterExcludedTables(Container $container)
    {
        $mlpTables = $container->get(self::MLP_TABLES);
        $siteDuplicator = $container[SiteDuplicator::class];
        add_filter(
            $siteDuplicator::FILTER_EXCLUDED_TABLES,
            wpHookProxy(static function () use ($mlpTables): array {
                return $mlpTables;
            })
        );
    }
}
