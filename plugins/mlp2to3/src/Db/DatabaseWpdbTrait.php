<?php

namespace Inpsyde\MultilingualPress2to3\Db;

use Exception;
use Throwable;
use Traversable;
use UnexpectedValueException;
use wpdb as Wpdb;

/**
 * Functionality for running database operations using WPDB.
 *
 * @package MultilingualPress2to3
 */
trait DatabaseWpdbTrait
{

    /**
     * Inserts a record defined by provided data into a table with the specified name.
     *
     * @param string $name The name of the table to insert into.
     * @param array|object $data The data of the record to insert.
     *
     * @return int|null The inserted row's ID, if applicable; otherwise null;
     *
     * @throws Exception If problem inserting.
     * @throws Throwable If problem running method.
     */
    protected function _insert($name, $data)
    {
        $data = (array) $data;
        $db = $this->_getDb();

        $db->insert($name, $data);
        $error = $db->last_error;
        $insertId = $db->insert_id;

        // Error while inserting
        if (empty($result) && !empty($error)) {
            throw new Exception($error);
        }

        // No new insert ID generated
        if ($insertId === 0) {
            return null;
        }

        return $insertId;
    }

    /**
     * Retrieves rows from the database matching the query conditions.
     *
     * @param string $query A query with optional `sprintf()` style placeholders.
     * @param array $values A list of values for the query placeholders.
     *
     * @return object[] A list of records.
     *
     * @throws Throwable If problem selecting.
     */
    protected function _select(string $query, $values = [])
    {
        $db = $this->_getDb();

        // Preparing query
        if (!empty($values)) {
            $prepared = $db->prepare($query, $values);

            if (!empty($query) && empty($prepared)) {
                throw new UnexpectedValueException($this->__('Could not prepare query "%1$s"', [$query]));
            }

            $query = $prepared;
        }

        $result = $db->get_results($query, 'OBJECT');
        $error = $db->last_error;

        // Error while selecting
        if (empty($result) && !empty($error)) {
            throw new Exception($this->__('Error selecting with query "%1$s":
            "%2$s"', [$query, $error]));
        }

        return $result;
    }

    /**
     * Creates a new table according to the specified parameters.
     *
     * @param string $tableName The name of the table to create.
     * @param array<string, array<int, mixed>> $fields A map of field names to field descriptors.
     * Each descriptor has the following keys, where asterisk (*) marks optional keys:
     *   type           Type of the field. Any of the allowed types. Case-insensitive.
     *   typemod*       A type modifier, such as "UNSIGNED" for numeric values.
     *   size*          The size of the field. If specified, will be added to the type verbatim.
     *   default*       If specified, will be added as the default value for the field.
     *                  Boolean, null, int, and float will not be quoted; everything else will be.
     *   null*          Whether or not the field allows NULL.
     *   autoincrement* Whether or not this field should be auto-incremented.
     * If the descriptor is not an array, it is assumed to be the type. This can be used as shorthand.
     * @param string[] $primaryKeys A list of field names which make the primary keys of the table.
     * All primary keys must be described in $fields.
     *
     * @return void
     *
     * @throws Exception If a primary key field is specified but not defined.
     * @throws Exception If a field with an empty name is specified.
     * @throws Exception If a field with an empty type is specified.
     * @throws Exception If problem making the change to the database.
     * @throws Throwable If problem creating.
     */
    protected function _createTable(string $tableName, array $fields, array $primaryKeys)
    {
        $db = $this->_getDb();
        $charset = $db->get_charset_collate();
        $query = "CREATE TABLE $tableName (\n";

        // Validating primary keys
        foreach ($primaryKeys as $key) {
            if (!array_key_exists($key, $fields)) {
                throw new Exception($this->__('No field descriptor specified for primary key "%1$s"', [$key]));
            }
        }

        foreach ($fields as $name => $field) {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception($this->__('Fields are required to have a non-empty name'));
            }

            if (!is_array($field)) {
                $field = ['type' => (string) $field];
            }

            $type = isset($field['type']) ? strtolower(trim($field['type'])) : null;
            if (empty($type)) {
                throw new Exception($this->__('Field "%1$s" is required to have a non-empty type', [$name]));
            }

            $typeMod = isset($field['typemod']) ? strtoupper(trim($field['typemod'])) : null;
            $size = $field['size'] ?? null;
            $isNull = $field['null'] ?? true;
            $isAutoincrement = $field['autoincrement'] ?? false;

            $query .= "$name $type";
            $query .= !empty($size) ? "($size)" : '';
            $query .= !empty($typeMod) ? " $typeMod" : '';
            if (array_key_exists('default', $field)) {
                $default = $field['default'] ?? null;
                if (is_null($default)) {
                    $default = 'NULL';
                } elseif (is_int($default) || is_float($default)) {
                    $default = (string) $default;
                } elseif (is_bool($default)) {
                    $default = $default ? 'true' : 'false';
                } else {
                    $default = "'$default'";
                }

                $query .= " DEFAULT $default";
            }

            $query .= $isNull ? ' NULL' : ' NOT NULL';
            $query .= $isAutoincrement ? ' AUTO_INCREMENT' : '';
            $query .= ",\n";
        }

        $query .= sprintf('PRIMARY KEY  (%1$s)' . PHP_EOL, implode(',', $primaryKeys));
        $query .= ") $charset;";

        $this->_alterDb($query);
    }

    /**
     * Renames a table.
     *
     * @param string $sourceName The name of the table to rename.
     * @param string $targetName The new name.
     *
     * @throws Exception If problem renaming.
     * @throws Throwable If problem running.
     */
    protected function _renameTable(string $sourceName, string $targetName)
    {
        $query = 'ALTER TABLE %1$s RENAME %2$s';
        $query = sprintf(
            $query,
            $this->_quoteIdentifier($sourceName),
            $this->_quoteIdentifier($targetName)
        );

        $this->_query($query);
    }

    /**
     * Drops a table.
     *
     * @param string $tableName The name of the table to drop.
     *
     * @throws Exception If problem dropping.
     * @throws Throwable If problem running.
     */
    protected function _dropTable(string $tableName)
    {
        $query = 'DROP TABLE %1$s';
        $query = sprintf(
            $query,
            $this->_quoteIdentifier($tableName)
        );

        $this->_query($query);
    }

    /**
     * Runs a query on the database.
     *
     * @param string $query The query to run.
     *
     * @return int The number of affected or selected rows if query affects or selects rows;
     * otherwise, `1`.
     *
     * @throws Throwable
     */
    protected function _query(string $query)
    {
        $db = $this->_getDb();
        $result = $db->query($query);

        if ($result === false) {
            throw new Exception($db->last_error);
        }

        return $result;
    }

    /**
     * Retrieves a string that represents fields in an SQL statement.
     *
     * Automatically quotes identifiers.
     * Allows an aliase for every field.
     *
     * @param array<int|string, string>|object|Traversable $fields A map of field positions to field names.
     * If a key is an integer, the field will be included as is.
     * If a key is a string, the key will be used as the field name, and value as alias.
     * @param bool Whether to quote the source field name, or leave it as is, when aliasing.
     *
     * @return string A string of field names usable in a SELECT expression.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getSelectFieldsString($fields, bool $quoteFields = true): string
    {
        $parts = [];
        foreach ($fields as $key => $value) {
            $parts[] = is_int($key)
                ? $this->_quoteIdentifier($value)
                : sprintf(
                    '%1$s AS %2$s',
                    $quoteFields ? $this->_quoteIdentifier($key) : $key,
                    $this->_quoteIdentifier($value)
                );
        }

        $string = implode(',', $parts);

        return $string;
    }

    /**
     * Quotes the specified identifier for use in an SQL statement.
     *
     * @param string $identifier The identifier to quote. A table or field name or alias.
     * If it contains a period, such as a fully qualified field name e.g. "mytable.my_field",
     * the two parts will be quoted separately.
     *
     * @return string The identifier, quoted.
     *
     * @throws Throwable If problem quoting.
     */
    protected function _quoteIdentifier(string $identifier): string
    {
        if (strpos($identifier, '.')) {
            $parts = explode('.', $identifier, 2);
            $identifier = "{$parts[0]}`.`{$parts[1]}";
        }
        return "`$identifier`";
    }

    /**
     * Alters the table to accommodate the table structure in the given query.
     *
     * @param string $query The SQL to alter the table with.
     *
     * @throws Exception If alteration query results in an error.
     * @throws Throwable If problem altering.
     */
    protected function _alterDb(string $query)
    {
        $db = $this->_getDb();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($query);

        $error = $db->last_error;
        if (!empty($error)) {
            throw new Exception($error);
        }
    }

    /**
     * Prefixes a table name.
     *
     * @param string $name The name to prefix.
     *
     * @return string The prefixed name.
     *
     * @throws Throwable If problem prefixing.
     */
    protected function _getPrefixedTableName($name)
    {
        $prefix = $this->_getDb()->prefix;

        return "{$prefix}$name";
    }

    /**
     * Retrieves the WPDB adapter used by this instance.
     *
     * @return Wpdb
     *
     * @throws Throwable
     */
    abstract protected function _getDb();

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);
}
