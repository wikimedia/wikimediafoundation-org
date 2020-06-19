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

/**
 * A handler that creates a table according to its configuration.
 *
 * @package MultilingualPress2to3
 */
class CreateTableHandler implements HandlerInterface
{
    use DatabaseWpdbTrait;

    use StringTranslatorConsumingTrait;
    use StringTranslatorAwareTrait;

    /**
     * @var Wpdb
     */
    protected $db;
    /**
     * @var string
     */
    protected $tableName;
    /**
     * @var array
     */
    protected $fields;
    /**
     * @var array
     */
    protected $primaryKeys;

    /**
     * @param Wpdb $db The DB adapter.
     * @param string $tableName The name of the table to create.
     * @param array $fields Fields for the new table. See {@see DatabaseWpdbTrait#_createTable()}.
     * @param array $primaryKeys A list of field names that make the primary key.
     */
    public function __construct(
        Wpdb $db,
        string $tableName,
        array $fields,
        array $primaryKeys
    ) {
        $this->db = $db;
        $this->tableName = $tableName;
        $this->fields = $fields;
        $this->primaryKeys = $primaryKeys;
    }

    /**
     * Creates a table.
     *
     * @throws Throwable If problem running.
     */
    public function run()
    {
        $this->_createTable(
            $this->_getTableName($this->tableName),
            $this->fields,
            $this->primaryKeys
        );
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
}