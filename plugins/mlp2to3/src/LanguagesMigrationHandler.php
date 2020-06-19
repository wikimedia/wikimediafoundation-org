<?php

namespace Inpsyde\MultilingualPress2to3;

use cli\Progress;
use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Inpsyde\MultilingualPress2to3\Migration\LanguageMigrator;
use Inpsyde\MultilingualPress2to3\Migration\RedirectMigrator;
use League\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use Throwable;
use WP_Site;
use wpdb as Wpdb;

/**
 * Migrates a series of language repository records from MLP2 to MLP3 format.
 *
 * @package MultilingualPress2to3
 */
class LanguagesMigrationHandler implements HandlerInterface
{
    use DatabaseWpdbTrait;

    use StringTranslatorConsumingTrait;
    use StringTranslatorAwareTrait;

    /**
     * @var LanguageMigrator
     */
    protected $migrator;
    /**
     * @var Progress
     */
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
     * @param LanguageMigrator $migrator The migrator that migrates languages.
     * @param Wpdb $db The DB adapter.
     * @param Progress $progress The progress that tracks migration... progress.
     * @param int $limit How many records to migrate. 0 means no limit.
     */
    public function __construct(
        LanguageMigrator $migrator,
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
     * Migrates a number of languages.
     *
     * @throws Throwable If problem running.
     */
    public function run()
    {
        $langs = $this->_getLanguagesToMigrate();
        $count = count ($langs);
        $progress = $this->_getProgress($count);

        foreach ($langs as $redirect) {
            $this->_getMigrator()->migrate($redirect);
            $progress->tick();
        }
//
        $progress->finish();
    }

    /**
     * Retrieves MLP2 languages to migrate to MLP3.
     *
     * @return object[] A list of objects, each representing an MLP2 language.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getLanguagesToMigrate()
    {
        $sourceLangs = $this->_getSourceLanguages();

        return $sourceLangs;
    }

    /**
     * Retrieves the languages to migrate.
     *
     * @return object[] A list of objects, each representing a source language.
     *
     * @throws Throwable If problem running.
     */
    protected function _getSourceLanguages()
    {
        $tableName = $this->_getTableName('mlp_languages');
        $limit = $this->_getLimit();
        $fields = [
            'id',
            'english_name',
            'native_name',
            'custom_name',
            'is_rtl',
            'iso_639_1',
            'iso_639_2',
            'priority',
            'wp_locale'         => 'locale',
            'http_name'         => 'bcp47',
        ];
        $fieldsString = $this->_getSelectFieldsString($fields);

        $query = 'SELECT %1$s FROM %2$s';
        $values = [$fieldsString, $this->_quoteIdentifier($tableName)];

        if (!empty($limit)) {
            $query .= ' LIMIT %3$s';
            $values[] = $limit;
        }

        $result = $this->_select($query, $values);

        return $result;
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
     * @return LanguageMigrator A list of handlers.
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

    /**
     * Retrieves the maximal amount of records to migrate.
     *
     * @return int The limit.
     *
     * @throws Throwable If problem running.
     */
    protected function _getLimit(): int
    {
        return $this->limit;
    }
}