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
 * A handler that renames a table according to its configuration.
 *
 * @package MultilingualPress2to3
 */
class RenameTableHandler implements HandlerInterface
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
    protected $sourceName;
    /**
     * @var string
     */
    protected $targetName;
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
     * @param string $sourceName The name of the table to rename.
     * @param string $targetName The new name.
     */
    public function __construct(
        Wpdb $db,
        string $sourceName,
        string $targetName
    ) {
        $this->db = $db;
        $this->sourceName = $sourceName;
        $this->targetName = $targetName;
    }

    /**
     * Creates a table.
     *
     * @throws Throwable If problem running.
     */
    public function run()
    {
        $this->_renameTable(
            $this->_getTableName($this->sourceName),
            $this->_getTableName($this->targetName)
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