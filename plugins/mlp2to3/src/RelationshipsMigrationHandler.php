<?php

namespace Inpsyde\MultilingualPress2to3;

use cli\Progress;
use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Throwable;
use wpdb as Wpdb;

class RelationshipsMigrationHandler implements HandlerInterface
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
     * @param ContentRelationshipMigrator $migrator The migrator that migrates relationships.
     * @param Wpdb $db The DB adapter.
     * @param Progress $progress The progress that tracks migration... progress.
     * @param int $limit How many relationships to migrate. 0 means no limit.
     */
    public function __construct(
        ContentRelationshipMigrator $migrator,
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
        $relationships = $this->_getRelationshipsToMigrate();
        $count = count ($relationships);
        $progress = $this->_getProgress($count);

        foreach ($relationships as $relationship) {
            $this->_getMigrator()->migrate($relationship);
            $progress->tick();
        }

        $progress->finish();
    }

    /**
     * Retrieves MLP2 links to migrate to MLP3.
     *
     * @return object[] A list of objects, each representing an MLP2 relationship.
     * A relationship corresponds to a record of the `multilingual_linked` table.
     *
     * @throws Throwable If problem retrieving relationships.
     */
    protected function _getRelationshipsToMigrate()
    {
        $table = $this->_getTableName('multilingual_linked');
        $limit = $this->_getLimit();
        $fields = $this->_getSelectFieldsString([
            'ml_id'                 => 'id',
            'ml_source_blogid'      => 'source_blog_id',
            'ml_source_elementid'   => 'source_element_id',
            'ml_blogid'             => 'target_blog_id',
            'ml_elementid'          => 'target_element_id',
            'ml_type'               => 'type',
        ]);
        $query = "SELECT {$fields} FROM {$table}";
        $query .= ($limit > 0 ? sprintf(' LIMIT %1$d', abs($limit)) : '');
        $result = $this->_select($query);

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
     * @return ContentRelationshipMigrator A list of handlers.
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