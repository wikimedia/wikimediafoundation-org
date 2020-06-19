<?php

namespace Inpsyde\MultilingualPress2to3;

use cli\Progress;
use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Migration\ModulesMigrator;
use Throwable;
use wpdb as Wpdb;

class ModulesMigrationHandler implements HandlerInterface
{
    use DatabaseWpdbTrait;

    use StringTranslatorConsumingTrait;
    use StringTranslatorAwareTrait;

    protected $migrator;
    protected $progress;

    /**
     * @var Wpdb
     */
    protected $db;
    /**
     * @var int
     */
    protected $limit;

    /**
     * Handler constructor.
     *
     * @param ModulesMigrator $migrator The migrator that migrates modules.
     * @param Wpdb $db The DB adapter.
     * @param Progress $progress The progress that tracks migration... progress.
     * @param int $limit How many records to migrate. 0 means no limit.
     */
    public function __construct(
        ModulesMigrator $migrator,
        Wpdb $db,
        Progress $progress,
        int $limit
    ) {
        $this->migrator = $migrator;
        $this->progress = $progress;
        $this->db = $db;
        $this->limit = $limit;
    }

    /**
     * Migrates a number of modules.
     *
     * @throws Throwable If problem running.
     */
    public function run()
    {
        $modules = $this->_getModulesToMigrate();
        $count = count ($modules);
        $progress = $this->_getProgress($count);

        foreach ($modules as $redirect) {
            $this->_getMigrator()->migrate($redirect);
            $progress->tick();
        }

        $progress->finish();
    }

    /**
     * Retrieves MLP2 links to migrate to MLP3.
     *
     * @return object[] A list of objects, each representing an MLP2 redirect.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getModulesToMigrate()
    {
        $limit = $this->_getLimit();
        $modulesMap = $this->_getNetworkOption('state_modules', []);

        if ($this->_isWooCommerceActive()) {
            $modulesMap['class-Mlp_WooCommerce_Module'] = 'on';
        }

        $modules = [];

        foreach ($modulesMap as $key => $value) {
            $modules[] = (object) [
                'name'      => $key,
                'status'    => $value,
            ];
        }

        if ($limit) {
            $modules = array_slice($modules, 0, $limit);
        }

        return $modules;
    }

    /**
     * Retrieves the value of a network option with the specified name.
     *
     * @param string $optionName The name of the option.
     * @param mixed $default The value to retrieve if the option does not exist.
     * @return mixed The value of the option.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getNetworkOption(string $optionName, $default)
    {
        return get_site_option($optionName, $default);
    }

    /**
     * Determines whether the WooCommerce plugin is active.
     *
     * @return bool True if the plugin is active; false otherwise.
     *
     * @throws Throwable If problem determining.
     */
    protected function _isWooCommerceActive()
    {
        if (!defined('WC_PLUGIN_FILE')) {
            return false;
        }

        $basename = plugin_basename(WC_PLUGIN_FILE);

        return is_plugin_active($basename);
    }

    /**
     * Retrieves the database driver associated with this instance.
     *
     * @return Wpdb The database driver.
     *
     * @throws Throwable If problem retrieving driver.
     */
    protected function _getDb()
    {
        return $this->db;
    }

    /**
     * Retrieves the table name corresponding to the given identifier.
     *
     * @param string $name The table identifier.
     * @return string The table name.
     *
     * @throws Throwable If problem retrieving table name.
     */
    protected function _getTableName(string $name)
    {
        return $this->_getPrefixedTableName($name);
    }

    /**
     * Retrieves the list of handlers associated with this instance.
     *
     * @return ModulesMigrator A list of handlers.
     */
    protected function _getMigrator()
    {
        return $this->migrator;
    }

    /**
     * Retrieves the progress element associated with this instance.
     *
     * @return Progress The progress.
     */
    protected function _getProgress($count = null): Progress
    {
        $progress = $this->progress;

        if (is_int($count)) {
            $progress->reset($count);
        }

        return $progress;
    }

    protected function _getLimit(): int
    {
        return $this->limit;
    }
}