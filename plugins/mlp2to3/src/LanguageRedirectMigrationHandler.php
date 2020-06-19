<?php

namespace Inpsyde\MultilingualPress2to3;

use cli\Progress;
use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Inpsyde\MultilingualPress2to3\Migration\LanguageRedirectMigrator;
use Inpsyde\MultilingualPress2to3\Migration\RedirectMigrator;
use Throwable;
use WP_Site;
use wpdb as Wpdb;

class LanguageRedirectMigrationHandler implements HandlerInterface
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
     * @param LanguageRedirectMigrator $migrator The migrator that migrates language redirects.
     * @param Wpdb $db The DB adapter.
     * @param Progress $progress The progress that tracks migration... progress.
     * @param int $limit How many records to migrate. 0 means no limit.
     */
    public function __construct(
        LanguageRedirectMigrator $migrator,
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
     * Migrates a number of language redirects.
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
     * Retrieves MLP2 language redirects to migrate to MLP3.
     *
     * @return object[] A list of objects, each representing an MLP2 language redirect.
     * Each object has the following properties:
     * - `user_id` - The ID of the user.
     * - `is_redirect` - Whether or not the redirection is enabled, 1 or 0 respectively.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getRedirectsToMigrate()
    {
        $limit = $this->_getLimit();
        $optionName = 'mlp_redirect';
        $userOptionsTable = $this->_getTableName('usermeta');

        $fields = [
            'user_id',
            'meta_value'         => 'is_redirect',
        ];
        $fieldsString = $this->_getSelectFieldsString($fields);
        $query = 'SELECT %1$s FROM %2$s WHERE %3$s = "%4$s"';
        $params = [
            $fieldsString,
            $this->_quoteIdentifier($userOptionsTable),
            $this->_quoteIdentifier('meta_key'),
            $optionName
        ];

        if ($limit) {
            $query .= ' LIMIT %5$d';
            $params[] = $limit;
        }

        $result = $this->_select($query, $params);

        return $result;
    }

    /**
     * Retrieves the value of a specified option, for the user with the specified ID.
     *
     * @param string $optionName The name of the option to retrieve.
     * @param int $userId The ID of the user to retrieve the option for.
     *
     * @return mixed The option value.
     */
    protected function _getUserOption(string $optionName, int $userId)
    {
        return get_user_option($optionName, $userId);
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
     * @return LanguageRedirectMigrator A list of handlers.
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