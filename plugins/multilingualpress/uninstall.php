<?php

/**
 * Uninstall routines.
 *
 * This file is called automatically when the plugin is deleted per user interface.
 *
 * @see https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
 */

# -*- coding: utf-8 -*-

declare(strict_types=1);

namespace Inpsyde\MultilingualPress;

use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Onboarding\State;
use Inpsyde\MultilingualPress\Database\Table;
use Inpsyde\MultilingualPress\Installation\Uninstaller;

defined('ABSPATH') || die();

if (!defined('WP_UNINSTALL_PLUGIN')) {
    return;
}

if (!current_user_can('activate_plugins')) {
    return;
}

if (!is_multisite()) {
    return;
}

$mainPluginFile = __DIR__ . '/multilingualpress.php';

if (
    plugin_basename($mainPluginFile) !== WP_UNINSTALL_PLUGIN
    || !is_readable($mainPluginFile)
) {
    unset($mainPluginFile);

    return;
}

/** @noinspection PhpIncludeInspection */
require_once $mainPluginFile;

unset($mainPluginFile);

if (!bootstrap()) {
    return;
}

$uninstaller = resolve(Uninstaller::class);

$uninstaller->uninstallTables(
    [
        resolve(Table\ContentRelationsTable::class),
        resolve(Table\LanguagesTable::class),
        resolve(Table\RelationshipsTable::class),
        resolve(Table\SiteRelationsTable::class),
    ]
);

$uninstaller->deleteNetworkOptions(
    [
        Activation\Activator::OPTION,
        Core\Admin\SiteSettingsRepository::OPTION,
        Core\PostTypeRepository::OPTION,
        Core\TaxonomyRepository::OPTION,
        Framework\Module\ModuleManager::OPTION,
        MultilingualPress::OPTION_VERSION,
        CacheSettingsRepository::OPTION_NAME,
    ]
);

$uninstaller->deleteSiteOptions(
    [
        Module\Redirect\Settings\Repository::OPTION_SITE,
        Module\Redirect\Settings\Repository::MODULE_SETTINGS,
    ]
);

$uninstaller->deletePostMeta(
    [
        Module\Trasher\TrasherSettingRepository::META_KEY,
        NavMenu\ItemRepository::META_KEY_SITE_ID,
    ]
);

$uninstaller->deleteUserMeta(
    [
        Module\Redirect\Settings\Repository::META_KEY_USER,
    ]
);

$uninstaller->deleteOnboardingData(
    [
        Onboarding\Onboarding::OPTION_ONBOARDING_DISMISSED,
        State::OPTION_NAME,
    ],
    [
        Core\Admin\Pointers\Pointers::USER_META_KEY,
    ]
);

$uninstaller->deleteScheduledEvents(
    [
        SiteDuplication\Schedule\AttachmentDuplicatorScheduler::SCHEDULE_HOOK,
        Schedule\Scheduler::ACTION_CLEANUP,
    ]
);

unset($uninstaller);
