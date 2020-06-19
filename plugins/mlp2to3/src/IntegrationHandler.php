<?php

namespace Inpsyde\MultilingualPress2to3;

use Dhii\I18n\StringTranslatingTrait;
use Inpsyde\MultilingualPress2to3\Config\ConfigAwareTrait;
use Inpsyde\MultilingualPress2to3\Event\WpHookingTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Handler\ControllerTrait;
use Psr\Container\ContainerInterface;

/**
 * Responsible for handling integration with MLP2, MLP3, and between them.
 *
 * @package MultilingualPress2to3
 */
class IntegrationHandler implements HandlerInterface
{
    use ControllerTrait;

    use ConfigAwareTrait;

    use WpHookingTrait;

    /**
     * Handler constructor.
     *
     * @param ContainerInterface $config The configuration of this handler.
     */
    public function __construct(ContainerInterface $config)
    {
        $this->_setConfigContainer($config);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->_hook();
    }

    /**
     * {@inheritdoc}
     */
    protected function _hook()
    {
        $this->_preventLegacyCheck();
        $this->_preventSharedTableDeletion();
    }

    /**
     * Prevents the which blocks activation of MLP3 while MLP2 is present.
     *
     * Without this, it is not possible to activate MLP3 if MLP2 is installed,
     * even if inactive.
     */
    protected function _preventLegacyCheck()
    {
        $filter = $this->_getConfig('filter_is_check_legacy');
        assert(is_string($filter) && !empty($filter));

        $this->_addFilter($filter, '__return_false');
    }

    /**
     * Prevents the deletion of tables that re shared between MLP2 and MLP3.
     */
    protected function _preventSharedTableDeletion()
    {
        // Prevents deletion of tables that have the same name in MLP2 and MLP3
        $filter = $this->_getConfig('filter_deleted_tables');
        assert(is_string($filter) && !empty($filter));

        $this->_addFilter($filter, function ($tableNames) {
            return $this->_removeSharedTableNames($tableNames);
        });
    }

    /**
     * Removes names of tables that are shared between MLP2 and MLP3.
     *
     * @param string[] $allNames A list of table names.
     * @return string[] A list of table names, none of which are shared between MLP2 and MLP3.
     */
    protected function _removeSharedTableNames($allNames)
    {
        $sharedNames = $this->_getConfig('shared_table_names');
        $nonSharedNames = array_diff($allNames, $sharedNames);

        return $nonSharedNames;
    }
}
