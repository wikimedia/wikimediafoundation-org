<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Installation;

use Inpsyde\MultilingualPress\Framework\Database\Table;
use Inpsyde\MultilingualPress\Framework\Database\TableInstaller;

/**
 * MultilingualPress installer.
 */
class Installer
{
    /**
     * @var TableInstaller
     */
    private $tableInstaller;

    /**
     * @param TableInstaller $tableInstaller
     */
    public function __construct(TableInstaller $tableInstaller)
    {
        $this->tableInstaller = $tableInstaller;
    }

    /**
     * Installs the given tables.
     *
     * @param Table[] ...$tables
     */
    public function installTables(Table ...$tables)
    {
        foreach ($tables as $table) {
            $this->tableInstaller->install($table);
        }
    }
}
