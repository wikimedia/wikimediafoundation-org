<?php
/**
 * Declares project configuration.
 *
 * @package MultilingualPress2to3
 */

use Dhii\Cache\MemoryMemoizer;
use Dhii\Cache\SimpleCacheInterface;
use Dhii\Data\Container\WritableContainerInterface;
use Dhii\Di\ContainerAwareCachingContainer;
use Dhii\I18n\FormatTranslatorInterface;
use cli\Progress;
use Dhii\Util\String\StringableInterface;
use Dhii\Wp\Containers\Options\BlogOptions;
use Dhii\Wp\Containers\Options\BlogOptionsContainer;
use Dhii\Wp\Containers\Options\SiteMeta;
use Dhii\Wp\Containers\Options\SiteMetaContainer;
use Dhii\Wp\Containers\Sites;
use Dhii\Wp\I18n\FormatTranslator;
use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress2to3\CreateTableHandler;
use Inpsyde\MultilingualPress2to3\FileContents;
use Inpsyde\MultilingualPress2to3\Handler\CompositeHandler;
use Inpsyde\MultilingualPress2to3\Handler\CompositeProgressHandler;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Index;
use Inpsyde\MultilingualPress2to3\Json;
use Inpsyde\MultilingualPress2to3\LanguageRedirectMigrationHandler;
use Inpsyde\MultilingualPress2to3\LanguagesMigrationHandler;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Inpsyde\MultilingualPress2to3\IntegrationHandler;
use Inpsyde\MultilingualPress2to3\MainHandler;
use Inpsyde\MultilingualPress2to3\MigrateCliCommand;
use Inpsyde\MultilingualPress2to3\MigrateCliCommandHandler;
use Inpsyde\MultilingualPress2to3\Migration\LanguageMigrator;
use Inpsyde\MultilingualPress2to3\Migration\LanguageRedirectMigrator;
use Inpsyde\MultilingualPress2to3\Migration\ModulesMigrator;
use Inpsyde\MultilingualPress2to3\Migration\RedirectMigrator;
use Inpsyde\MultilingualPress2to3\Migration\SiteLanguageMigrator;
use Inpsyde\MultilingualPress2to3\Migration\TranslatablePostTypesMigrator;
use Inpsyde\MultilingualPress2to3\ModulesMigrationHandler;
use Inpsyde\MultilingualPress2to3\RedirectMigrationHandler;
use Inpsyde\MultilingualPress2to3\RelationshipsMigrationHandler;
use Inpsyde\MultilingualPress2to3\RemoveTableHandler;
use Inpsyde\MultilingualPress2to3\RenameTableHandler;
use Inpsyde\MultilingualPress2to3\SiteLanguagesMigrationHandler;
use Inpsyde\MultilingualPress2to3\TranslatablePostTypesMigrationHandler;
use Psr\Container\ContainerInterface;
use cli\progress\Bar;

/**
 * @param array $defaults Default values for services.
 * Expecting the following defaults to be passed from outside
 *
 * - 'version'      - The module version number.
 * - 'root_path'    - Path to the root of the application.
 * - 'base_path'    - Path to the root of the module.
 * - 'root_url'     - URL of the application.
 * - 'base_url'     - URL of the module.
 * - 'admin_url'    - URL of the admin panel.
 * - 'is_debug'     - Whether or not in debug mode.
 */
return function ( array $defaults ) {
	return array_merge($defaults, [
		'base_dir'                => function ( ContainerInterface $c ) {
			return dirname( $c->get( 'base_path' ) );
		},
        'plugins_dir'             => function (ContainerInterface $c) {
	        $basePath = $c->get('base_path');
	        $basename = plugin_basename($basePath);
	        $baseDir = str_replace($basename, '', $basePath);

	        return $baseDir;
        },
        'admin_dir'               => function (ContainerInterface $c) {
            return str_replace(
                $c->get('root_url') . '/',
                $c->get('root_path'),
                $c->get('admin_url')
            );
        },
		'js_path'                 => '/assets/js',
		'templates_dir'           => '/templates',
		'translations_dir'        => '/languages',
		'text_domain'             => 'mlp2to3',

        'wpcli_command_key_mlp2to3_migrate' => 'mlp2to3',
        'filter_is_check_legacy'  => 'multilingualpress.is_check_legacy',
        'filter_deleted_tables'   => 'multilingualpress.deleted_tables',

        'table_name_temp_languages' => 'mlp_languages_h7h2927fg2',
        'table_name_languages' => 'mlp_languages',
        'table_fields_languages' => function ():array  {
            return [
                LanguagesTable::COLUMN_ID => [
                    'type' => 'bigint',
                    'typemod' => 'unsigned',
                    'size' => 20,
                    'null' => false,
                    'autoincrement' => true,
                ],
                LanguagesTable::COLUMN_ENGLISH_NAME => [
                    'type' => 'tinytext',
                ],
                LanguagesTable::COLUMN_NATIVE_NAME => [
                    'type' => 'tinytext',
                ],
                LanguagesTable::COLUMN_CUSTOM_NAME => [
                    'type' => 'tinytext',
                ],
                LanguagesTable::COLUMN_ISO_639_1_CODE => [
                    'type' => 'varchar',
                    'size' => 8,
                ],
                LanguagesTable::COLUMN_ISO_639_2_CODE => [
                    'type' => 'varchar',
                    'size' => 8,
                ],
                LanguagesTable::COLUMN_ISO_639_3_CODE => [
                    'type' => 'varchar',
                    'size' => 8,
                ],
                LanguagesTable::COLUMN_LOCALE => [
                    'type' => 'varchar',
                    'size' => 20,
                ],
                LanguagesTable::COLUMN_BCP_47_TAG => [
                    'type' => 'varchar',
                    'size' => 20,
                ],
                LanguagesTable::COLUMN_RTL => [
                    'type' => 'tinyint',
                    'typemod' => 'unsigned',
                    'size' => 1,
                    'default' => 0,
                ],
            ];
        },

        'shared_table_names'             => [
            'mlp_languages',
            'mlp_site_relations',
        ],

        'table_keys_languages'           => function ():array {
            return [LanguagesTable::COLUMN_ID];
        },

        'mlp3_base_name' => 'multilingualpress/multilingualpress.php',

        'mlp3_base_path' => function (ContainerInterface $c): string {
            $baseName = $c->get('mlp3_base_name');
            $pluginsDir = $c->get('plugins_dir');
            $basePath = "$pluginsDir/$baseName";

            return $basePath;
        },

        'mlp3_base_dir' => function (ContainerInterface $c): string {
            $basePath = $c->get('mlp3_base_path');
            $baseDir = dirname($basePath);

            return $baseDir;
        },

        'embedded_languages_file_path' => function (ContainerInterface $c): string {
	        $baseDir = $c->get('mlp3_base_dir');
	        $path = "$baseDir/public/json/languages-wp.json";

	        return $path;
        },

        'embedded_languages_string' => function (ContainerInterface $c) {
	        $path = $c->get('embedded_languages_file_path');
	        $f = $c->get('file_content_factory');
	        $string = $f($path);

	        return $string;
        },

        'default_blog_option_value' => 'H=Kq^EQP5!G7E#dK',
        'default_site_meta_value' => 'z?!s4JWN76_5E??!',

        'sites' => function (ContainerInterface $c): ContainerInterface {
	        return new Sites();
        },

        'blog_options_factory' => function (ContainerInterface $c): callable {
	        $default = $c->get('default_blog_option_value');

            return function (int $blogId) use ($default): WritableContainerInterface {
                return new BlogOptions($blogId, $default);
            };
        },

        'blog_options_container' => function (ContainerInterface $c): ContainerInterface {
	        $sites = $c->get('sites');
            $optionsFactory = $c->get('blog_options_factory');
            $optionsContainer = new BlogOptionsContainer($optionsFactory, $sites);

            return $optionsContainer;
        },

        'blog_options' => function (ContainerInterface $c): WritableContainerInterface {
	        $container = $c->get('blog_options_container');
	        $currentSiteId = get_current_blog_id();
	        $options = $container->get($currentSiteId);

            return $options;
        },

        'site_meta_factory' => function (ContainerInterface $c): callable {
	        $default = $c->get('default_site_meta_value');

	        return function (int $siteId) use ($default): WritableContainerInterface {
                return new SiteMeta($siteId, $default);
            };
        },

        'site_meta_container' => function (ContainerInterface $c): ContainerInterface {
            $sites = $c->get('sites');
	        $metaFactory = $c->get('site_meta_factory');
	        $metaContainer = new SiteMetaContainer($metaFactory, $sites);

	        return $metaContainer;
        },

        'site_meta' => function (ContainerInterface $c) {
            $container = $c->get('site_meta_container');
            $currentSiteId = get_current_blog_id();
            $meta = $container->get($currentSiteId);

            return $meta;
        },

        'embedded_languages_json' => function (ContainerInterface $c) {
	        $string = $c->get('embedded_languages_string');
            $f = $c->get('json_factory');
            $json = $f($string);

            return $json;
        },

        'embedded_languages' => function (ContainerInterface $c) {
            $list = $c->get('embedded_languages_json');
            $key = 'iso-639-3';
            $field = function ($item) use ($key) {
                if ($item->type !== 'language' || !property_exists($item, $key)) {
                    // Item does not have the identifying key
                    return null;
                }

                return $item->{$key};
            };
            $f = $c->get('index_factory');
            $map = $f($list, $field);

            return $map;
        },

        'embedded_locales' => function (ContainerInterface $c) {
            $list = $c->get('embedded_languages_json');
            $key = 'bcp47';
            $field = function ($item) use ($key) {
                if ($item->type !== 'locale' || !property_exists($item, $key)) {
                    // Item does not have the identifying key
                    return null;
                }

                return $item->{$key};
            };
            $f = $c->get('index_factory');
            $map = $f($list, $field);

            return $map;
        },

        'index_factory' => function (ContainerInterface $c): callable {
	        return function ($data, callable $field): ContainerInterface {
	            return new Index($data, $field);
            };
        },

        'json_factory' => function (ContainerInterface $c): callable {

            return function ($json) use ($c) {
                $isDebug = $c->get('is_debug');

                return new Json($json, $isDebug);
            };
        },

        'file_content_factory' => function (ContainerInterface $c): callable {
	        return function (string $filePath) use ($c):StringableInterface {
                $isDebug = $c->get('is_debug');
                return new FileContents($filePath, $isDebug);
            };
        },

        /* The main handler */
        'handler_main' => function (ContainerInterface $c) {
            return new MainHandler($c, $c->get('handlers'));
        },

        /*
         * List of handlers to run
         */
        'handlers' => function (ContainerInterface $c) {
            return [
                $c->get('handler_migrate_cli_command'),
                $c->get('handler_integration'),
//                $c->get('handler_log'),
            ];
        },

        'translator' => function (ContainerInterface $c) {
            return new FormatTranslator($c->get('text_domain'));
        },

        'memory_cache_factory' => function (ContainerInterface $c): callable {
            return function () use($c): SimpleCacheInterface {
                return new MemoryMemoizer();
            };
        },

        'container_factory' => function (ContainerInterface $c): callable {
            $cacheFactory = $c->get('memory_cache_factory');
            return function (array $data) use ($cacheFactory, $c) {
                $cache = $cacheFactory();
                assert($cache instanceof SimpleCacheInterface);

                return new ContainerAwareCachingContainer($data, $cache, $c);
            };
        },

        'composite_handler_factory' => function (ContainerInterface $c): callable {
            return function (array $handlers) {
                return new CompositeHandler($handlers);
            };
        },

        'composite_progress_handler_factory' => function (ContainerInterface $c): callable {
            return function (array $handlers, Progress $progress): HandlerInterface {
                return new CompositeProgressHandler($handlers, $progress);
            };
        },

        'migrations_handler_factory' => function (ContainerInterface $c): callable {
            $progressHandlerFactory = $c->get('composite_progress_handler_factory');

            return function ($handlers) use ($progressHandlerFactory, $c): HandlerInterface {
                $progress = $c->get('migration_progress');
                $handler = $progressHandlerFactory($handlers, $progress);

                return $handler;
            };
        },

        /**
         * Provides a layer for name-based lazy-loading of migration modules
         */
        'migration_modules' => function (ContainerInterface $c) {
            $f = $c->get('container_factory');
            assert(is_callable($f));

            return $f($c->get('migration_module_definitions'));
        },

        'migration_module_names' => function (ContainerInterface $c) {
            $definitions = $c->get('migration_module_definitions');
            assert(is_array($definitions));

            return array_keys($definitions);
        },

        'migration_module_definitions' => function (ContainerInterface $c) {
            return [
                'relationships'         => function (ContainerInterface $c) {
                    return $c->get('handler_relationships_migration');
                },
                'redirects'             => function (ContainerInterface $c) {
                    return $c->get('handler_redirect_migration');
                },
                'modules'               => function (ContainerInterface $c) {
                    return $c->get('handler_modules_migration');
                },
                'lang_redirects'               => function (ContainerInterface $c) {
                    return $c->get('handler_language_redirect_migration');
                },
                'translatable_post_types'      => function (ContainerInterface $c) {
                    return $c->get('handler_translatable_post_types_migration');
                },
                'languages'                 => function (ContainerInterface $c) {
                    return $c->get('handler_languages_migration_steps');
                },
                'site_languages'                 => function (ContainerInterface $c) {
                    return $c->get('handler_site_languages_migration');
                },
            ];
        },

        'handler_relationships_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new RelationshipsMigrationHandler(
                $c->get('migrator_relationships'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_redirect_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new RedirectMigrationHandler(
                $c->get('migrator_redirects'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_modules_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new ModulesMigrationHandler(
                $c->get('migrator_modules'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_language_redirect_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new LanguageRedirectMigrationHandler(
                $c->get('migrator_language_redirects'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_translatable_post_types_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new TranslatablePostTypesMigrationHandler(
                $c->get('migrator_translatable_post_types'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_languages_migration_steps' => function (ContainerInterface $c): HandlerInterface {
            return new CompositeHandler([
                $c->get('handler_create_languages_temp_table'),
                $c->get('handler_languages_migration'),
                $c->get('handler_activate_languages_temp_table')
            ]);
        },

        'handler_create_languages_temp_table' => function (ContainerInterface $c): HandlerInterface {
            return new CreateTableHandler(
                $c->get('wpdb'),
                $c->get('table_name_temp_languages'),
                $c->get('table_fields_languages'),
                $c->get('table_keys_languages')
            );
        },

        'handler_languages_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new LanguagesMigrationHandler(
                $c->get('migrator_language'),
                $c->get('wpdb'),
                $progress,
                0
            );
        },

        'handler_site_languages_migration' => function (ContainerInterface $c): HandlerInterface {
            $siteSettingsOptionName = 'inpsyde_multilingual';
            $migrator = $c->get('migrator_site_language');
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);
            $handler = new SiteLanguagesMigrationHandler(
                $migrator,
                $c->get('wpdb'),
                $progress,
                0,
                $c->get('main_site_id'),
                $siteSettingsOptionName
            );

            return $handler;
        },

        'migrator_language' => function (ContainerInterface $c): LanguageMigrator {
            return new LanguageMigrator(
                $c->get('wpdb'),
                $c->get('translator'),
                $c->get('embedded_locales'),
                $c->get('embedded_languages'),
                $c->get('table_name_temp_languages')
            );
        },

        'migrator_site_language' => function (ContainerInterface $c): SiteLanguageMigrator {
            $siteSetingsOptionName = 'multilingualpress_site_settings';
            $mainSiteId = $c->get('main_site_id');

            $blogOptionsContainer = $c->get('blog_options_container');
            assert($blogOptionsContainer instanceof ContainerInterface);

            $siteMetaContainer = $c->get('site_meta_container');
            assert($siteMetaContainer instanceof ContainerInterface);

            $mainSiteMeta = $siteMetaContainer->get($mainSiteId);

            $migrator = new SiteLanguageMigrator(
                $c->get('wpdb'),
                $c->get('translator'),
                $blogOptionsContainer,
                $mainSiteMeta,
                $siteSetingsOptionName
            );

            return $migrator;
        },

        'handler_activate_languages_temp_table' => function (ContainerInterface $c) {
            return new CompositeHandler([
                $c->get('handler_remove_languages_table'),
                $c->get('handler_rename_languages_temp_table'),
            ]);
        },

        'handler_remove_languages_table' => function (ContainerInterface $c) {
            return new RemoveTableHandler(
                $c->get('wpdb'),
                $c->get('table_name_languages')
            );
        },

        'handler_rename_languages_temp_table' => function (ContainerInterface $c) {
            return new RenameTableHandler(
                $c->get('wpdb'),
                $c->get('table_name_temp_languages'),
                $c->get('table_name_languages')
            );
        },

        'progress_bar_factory' => function (ContainerInterface $c) {
            return function (int $total = 1, string $message = '' ): Bar {
                return new Bar($message, $total);
            };
        },

        /*
         * Tracks total progress of migration.
         */
        'migration_progress' => function (ContainerInterface $c): Progress {
            $f = $c->get('progress_bar_factory');
            assert(is_callable($f));
            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return $f(1, $t->translate('Modules'));
        },

        /*
         * Tracks the progress of an individual migration module.
         */
        'migration_modules_progress' => function (ContainerInterface $c): Progress {
            $f = $c->get('progress_bar_factory');
            assert(is_callable($f));
            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return $f(1, $t->translate('Tasks'));
        },

        'handler_migrate_cli_command' => function (ContainerInterface $c) {
            return new MigrateCliCommandHandler($c);
        },

        'wpcli_command_migrate' => function (ContainerInterface $c) {
            return new MigrateCliCommand(
                $c->get('translator'),
                $c->get('migration_modules'),
                $c->get('migration_module_names'),
                $c->get('migrations_handler_factory')
            );
        },

        'migrator_relationships' => function (ContainerInterface $c) {
            return new ContentRelationshipMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_redirects' => function (ContainerInterface $c) {
            return new RedirectMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_modules' => function (ContainerInterface $c) {
            return new ModulesMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_language_redirects' => function (ContainerInterface $c) {
            return new LanguageRedirectMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_translatable_post_types' => function (ContainerInterface $c) {
            return new TranslatablePostTypesMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'wpdb' => function (ContainerInterface $c) {
            global $wpdb;

            return $wpdb;
        },

        'handler_integration' => function (ContainerInterface $c) {
            return new IntegrationHandler(
                $c
            );
        },

//        'handler_log' => function (ContainerInterface $c) {
//            return new LogHandler($c);
//        },
//
//        'log_controller' => function (ContainerInterface $c) {
//            if (function_exists('Inpsyde\Wonolog\bootstrap')) {
//                return \Inpsyde\Wonolog\bootstrap();
//            }
//
//            return null;
//        },
//
//        'log_listeners' => function (ContainerInterface $c) {
//            return [
//                $c->get('log_listener_relationships'),
//            ];
//        },
//
//        'log_listener_relationships' => function (ContainerInterface $c) {
//            return new RelationshipListener();
//        },
//
//        'data_hasher'               => function ( ContainerInterface $c ) {
//          return new DataHasher($c->get('data_hash_glue'), $c->get('data_hash_separator'));
//        }
    ]);
};
