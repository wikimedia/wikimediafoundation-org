<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3;

use cli\Progress;
use Dhii\I18n\FormatTranslatorInterface;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Exception;
use Inpsyde\MultilingualPress2to3\Cli\CliUtilsTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Psr\Container\ContainerInterface;
use Throwable;
use UnexpectedValueException;
use wpdb as Wpdb;

/**
 * The command responsible for migrating relationships.
 *
 * @package MultilingualPress2to3
 */
class MigrateCliCommand
{
    use CliUtilsTrait;

    use StringTranslatorConsumingTrait;

    const MODULE_NAMES_ALL = 'all';

    protected $translator;
    /**
     * @var ContainerInterface
     */
    protected $migrationModules;

    /**
     * Names of all available migration module names;
     *
     * @var string[]
     */
    protected $allMigrationModuleNames;
    /**
     * @var callable
     */
    protected $compositeHandlerFactory;

    /**
     * @param FormatTranslatorInterface $translator
     * @param HandlerInterface $migrationHandler
     * @param ContainerInterface $migrationModules
     * @param array $allMigrationModuleNames
     * @param callable $compositeHandlerFactory
     */
    public function __construct(
        FormatTranslatorInterface $translator,
        ContainerInterface $migrationModules,
        array $allMigrationModuleNames,
        callable $compositeHandlerFactory
    ) {
        $this->translator = $translator;
        $this->migrationModules = $migrationModules;
        $this->allMigrationModuleNames = $allMigrationModuleNames;
        $this->compositeHandlerFactory = $compositeHandlerFactory;
    }

    /**
     * Executes the command.
     *
     * @throws Throwable If problem executing.
     */
    public function __invoke($values, $args)
    {
        try {
            $moduleNames = $this->_normalizeModuleNames($values[0] ?? static::MODULE_NAMES_ALL);
            $modules = $this->migrationModules;
            $this->_validateModulesToMigrate($modules, $moduleNames);
            $modules = $this->_initModules($modules, $moduleNames);
            $handler = $this->_createCompositeHandler($modules);

            $handler->run();
        } catch (Exception $e) {
            $this->_outputError($e->getMessage());
            $this->_outputDebug((string) $e);
            $this->_exit(1);
        }

        $this->_outputSuccess($this->__('Migrated %1$d modules', [count($moduleNames)]));
    }

    protected function _normalizeModuleNames(string $names): array
    {
        $names = $this->_normalizeModuleName($names);

        $names = $names === static::MODULE_NAMES_ALL
            ? $this->allMigrationModuleNames
            : explode(',', $names);

        $names = array_map(function ($name) {
            return $this->_normalizeModuleName($name);
        }, $names);

        return $names;
    }

    protected function _normalizeModuleName(string $name): string
    {
        return trim(strtolower($name));
    }

    /**
     * @param ContainerInterface $modules
     * @param $moduleNames
     *
     * @throws UnexpectedValueException If one of the module names is invalid.
     */
    protected function _validateModulesToMigrate(ContainerInterface $modules, $moduleNames)
    {
        foreach ($moduleNames as $name) {
            if (!$modules->has($name)) {
                throw new UnexpectedValueException($this->__('Module "%1$s" does not exist', [$name]));
            }
        }
    }

    protected function _initModules(ContainerInterface $modules, array $moduleNames): array
    {
        $initialized = [];
        foreach ($moduleNames as $name) {
            $initialized[$name] = $modules->get($name);
        }

        return $initialized;
    }

    protected function _getTranslator()
    {
        return $this->translator;
    }

    protected function _createCompositeHandler($handlers): HandlerInterface
    {
        $f = $this->_getCompositeHandlerFactory();

        return $f($handlers);
    }

    protected function _getCompositeHandlerFactory(): callable
    {
        return $this->compositeHandlerFactory;
    }
}
