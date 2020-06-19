<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Migration;

use Dhii\I18n\FormatTranslatorInterface;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\I18n\StringTranslatorAwareTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Event\WpTriggerCapableTrait;
use Throwable;
use UnexpectedValueException;
use WP_CLI;
use wpdb as Wpdb;

/**
 * Migrates a single MLP2 module to MLP3.
 *
 * @package MultilingualPress2to3
 */
class ModulesMigrator
{
    use WpTriggerCapableTrait;

    use DatabaseWpdbTrait;

    use StringTranslatingTrait;

    use StringTranslatorAwareTrait;

    protected $db;
    protected $translator;

    /**
     * @param Wpdb $wpdb The database driver to use for DB operations.
     * @param FormatTranslatorInterface $translator The translator to use for i18n.
     */
    public function __construct(
        Wpdb $wpdb,
        FormatTranslatorInterface $translator
    )
    {
        $this->db = $wpdb;
        $this->translator = $translator;
    }

    /**
     * Migrates an MLP2 module to MLP3.
     *
     * @param object $mlp2Module Data of an MLP2 module. 2 properties required:
     * - `name` - The module name.
     * - `status` - The status of the module, 'on' or 'off' for on or off respectively.
     *
     * @throws Throwable If problem migrating.
     */
    public function migrate($mlp2Module)
    {
        $optionName = 'multilingualpress_modules';
        $obsoleteModules = $this->_getObsoleteModuleNames();
        $moduleName = $this->_getModuleName($mlp2Module->name);
        $moduleStatus = $this->_getModuleStatus($mlp2Module->status);

        // If obsolete, ignore
        if (in_array($moduleName, $obsoleteModules)) {
            return;
        }

        $modules = $this->_getNetworkOption($optionName, []);

        // If already exists and same value, nothing to migrate
        if (array_key_exists($moduleName, $modules) && $modules[$moduleName] === $moduleStatus) {
            return;
        }

        $modules[$moduleName] = $moduleStatus;

        $this->_setNetworkOption($optionName, $modules);
    }

    /**
     * Retrieves module names that are obsolete in MLP3.
     *
     * @return string[] A list of module names.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getObsoleteModuleNames(): array
    {
        return [
            'cpt_translator',
            'advanced_translator',
            'quicklink',
        ];
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
     * Assigns a value to a network option with the specified name.
     *
     * @param string $optionName Name of the option to set.
     * @param mixed $value The option value.
     *
     * @throws UnexpectedValueException If option could not be set.
     */
    protected function _setNetworkOption(string $optionName, $value)
    {
        $result = update_site_option($optionName, $value);

        if (!$result) {
            throw new UnexpectedValueException($this->__('Could not update network option "%1$s"', [$optionName]));
        }
    }

    /**
     * Retrieves an MLP3 module name for a key.
     *
     * @param string $key Module key.
     *
     * @return string Name of MLP3 module.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getModuleName(string $key): string
    {
        // Mapping exceptional cases
        $map = $this->_getModuleNameMap();
        if (isset($map[$key])) {
            return $map[$key];
        }

        return $this->_transformModuleName($key);
    }

    /**
     * Transforms an MLP2 module name to MLP3 format.
     *
     * @param string $name The module name to transform.
     *
     * @return string The transformed name.
     *
     * @throws Throwable If problem transforming.
     */
    protected function _transformModuleName(string $name): string
    {
        $prefix = 'class-mlp_';
        $suffix = '_module';

        $name = strtolower($name);
        $name = $this->_removePrefix($name, $prefix);
        $name = $this->_removeSuffix($name, $suffix);

        return $name;
    }

    /**
     * Retrieves a map of MLP2 module names to MLP3 module names.
     *
     * @return array<string, string> The module name map.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getModuleNameMap(): array
    {
        return [
            'class-Mlp_Redirect_Registration'       => 'redirect',
        ];
    }

    /**
     * Transforms an MLP2 module status to MLP3 format.
     *
     * @param string $status The module status to transform.
     *
     * @return bool The transformed status.
     *
     * @throws Throwable If problem transforming.
     */
    protected function _getModuleStatus(string $status): bool
    {
        $status = strtolower($status);

        if ($status === 'on') {
            return true;
        }

        if ($status === 'off') {
            return false;
        }

        throw new UnexpectedValueException(
            $this->__(
                'Invalid module status "%1$s"',
                [$status]
            )
        );
    }

    /**
     * Removes a prefix from the specified string, if it is found.
     *
     * @param string $string The string to remove the prefix from.
     * @param string $prefix The prefix to remove.
     *
     * @return string The string without the prefix.
     *
     * @throws Throwable If problem removing.
     */
    protected function _removePrefix(string $string, string $prefix): string
    {
        $length = strlen($prefix);
        $currentPrefix = substr($string, 0, $length);

        if ($currentPrefix !== $prefix) {
            return $string;
        }

        return substr($string, $length);
    }


    /**
     * Removes a suffix from the specified string, if it is found.
     *
     * @param string $string The string to remove the suffix from.
     * @param string $suffix The suffix to remove.
     *
     * @return string The string without the suffix.
     *
     * @throws Throwable If problem removing.
     */
    protected function _removeSuffix(string $string, string $suffix): string
    {
        $length = strlen($suffix);
        $currentSuffix = substr($string, $length * -1);

        if ($currentSuffix !== $suffix) {
            return $string;
        }

        return substr($string, 0, $length * -1);
    }

    /**
     * Retrieves a table name for a key.
     *
     * @param string $key The key to get the table name for.
     * @return string The table name.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getTableName($key)
    {
        return $this->_getPrefixedTableName($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getDb()
    {
        return $this->db;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getTranslator()
    {
        return $this->translator;
    }
}
