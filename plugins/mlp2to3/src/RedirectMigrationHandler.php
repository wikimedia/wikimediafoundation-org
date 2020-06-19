<?php

namespace Inpsyde\MultilingualPress2to3;

use cli\Progress;
use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Inpsyde\MultilingualPress2to3\Migration\RedirectMigrator;
use Throwable;
use WP_Site;
use wpdb as Wpdb;

class RedirectMigrationHandler implements HandlerInterface
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
     * @param RedirectMigrator $migrator The migrator that migrates redirects.
     * @param Wpdb $db The DB adapter.
     * @param Progress $progress The progress that tracks migration... progress.
     * @param int $limit How many records to migrate. 0 means no limit.
     */
    public function __construct(
        RedirectMigrator $migrator,
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
     * Migrates a number of relationships.
     *
     * @throws Throwable If problem running.
     */
    public function run()
    {
        $redirects = $this->_getRedirectsToMigrate();
        $count = count ($redirects);
        $progress = $this->_getProgress($count);

        foreach ($redirects as $redirect) {
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
    protected function _getRedirectsToMigrate()
    {
        $result = $this->_getSitesOption('inpsyde_multilingual_redirect');

        return $result;
    }

    /**
     * Retrieves a value of a site option, for each site.
     *
     * @param string $optionName The name of the option to retrieve for sites.
     *
     * @return object[] An list of objects, each having 2 properties:
     *  - `$optionName` - Whatever the $optionName parameter was.
     *  - `site_id` - The ID of the site, for which the option was retrieved.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getSitesOption(string $optionName)
    {
        $limit = $this->_getLimit();
        $selects = [];
        $siteOptionTables = $this->_getSiteOptionsTables();

        array_walk($siteOptionTables, function ($value, $key) use (&$selects, $optionName) {
            $fields = (object) [
                'option_value'  => $optionName,
            ];
            $fields->{(string) $key} = 'site_id'; // Otherwise numeric string keys are turned into integers
            $fieldsString = $this->_getSelectFieldsString($fields, false);
            $tableName = $value;
            $selects[] = sprintf(
                '(SELECT %1$s FROM %2$s WHERE %3$s = "%4$s")',
                $fieldsString,
                $this->_quoteIdentifier($tableName),
                $this->_quoteIdentifier('option_name'),
                $optionName
            );
        });

        $query = implode("\nUNION\n", $selects);

        if ($limit) {
            $query .= "\nLIMIT {$limit}";
        }

        $result = $this->_select($query);

        return $result;
    }

    /**
     * Retrieves tables for site options of each site.
     *
     * @return string[] A map of site ID to site options table name.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getSiteOptionsTables()
    {
        $sites = $this->_getSites();

        $idsToTables = [];

        foreach ($sites as $site) {
            $siteId = $site->id;
            $siteTable = $this->_getSiteOptionsTable($siteId);

            $idsToTables[$siteId] = $siteTable;
        }

        return $idsToTables;
    }

    /**
     * Retrieves a list of sites to migrate redirects for.
     *
     * @return WP_Site[] A list of sites.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getSites()
    {
        $sites = get_sites();

        return $sites;
    }

    /**
     * Retrieves the name of the table that stores options for a site with the specified ID.
     *
     * @param int $siteId The ID of the site to get the table name for.
     * @return string The table name.
     * @throws Throwable If problem retrieving.
     */
    protected function _getSiteOptionsTable(int $siteId): string
    {
        $table = $siteId === 1
            ? 'options'
            : vsprintf('%1$s_options', [$siteId]);

        return $this->_getTableName($table);
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
     * @return RedirectMigrator A list of handlers.
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