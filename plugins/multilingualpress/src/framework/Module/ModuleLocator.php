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

namespace Inpsyde\MultilingualPress\Framework\Module;

use ArrayIterator;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider;
use IteratorAggregate;
use RangeException;
use RuntimeException;
use Throwable;
use Traversable;
use UnexpectedValueException;

/**
 * Locates modules in module files.
 */
class ModuleLocator implements IteratorAggregate
{
    /**
     * The list of module file paths to load.
     *
     * @var Traversable
     */
    protected $moduleFiles;

    /**
     * @param Traversable $moduleFiles The list of module definition file paths.
     */
    public function __construct(Traversable $moduleFiles)
    {
        $this->moduleFiles = $moduleFiles;
    }

    /**
     * {@inheritdoc}
     *
     * @return ArrayIterator
     *
     * @throws Throwable If problem locating modules.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->locate());
    }

    /**
     * Retrieves a list of modules.
     *
     * @throws Throwable If problem locating modules.
     *
     * @return ServiceProvider[] A list of modules.
     */
    protected function locate(): array
    {
        $modules = [];

        foreach ($this->getModuleFiles() as $filePath) {
            $filePath = (string) $filePath;
            $modules[] = $this->createModule($filePath);
        }

        return $modules;
    }

    /**
     * Retrieves the list of module files.
     *
     * @return Traversable The list of absolute paths to module definition files.
     */
    protected function getModuleFiles(): Traversable
    {
        return $this->moduleFiles;
    }

    /**
     * Creates a module defined by the specified file.
     *
     * @param string $filePath The path to the file which defines the module.
     *
     * @throws UnexpectedValueException If the file defined by the specified path does not exist
     * or is not readable.
     * @throws RangeException If the file does not contain a valid module definition.
     * @throws RuntimeException If problem creating module.
     *
     * @return ServiceProvider The module defined in the file.
     */
    protected function createModule(string $filePath): ServiceProvider
    {
        if (!is_readable($filePath)) {
            throw new UnexpectedValueException(
                vsprintf('File "%1$s" does not exist or is not readable', [$filePath])
            );
        }

        $moduleFactory = include $filePath;

        if (!is_callable($moduleFactory)) {
            throw new RangeException(vsprintf('File "%1$s" did not return a valid callable', [$filePath]));
        }

        $module = $moduleFactory();

        if (!$module instanceof ServiceProvider) {
            throw new RangeException(
                vsprintf('The factory in file "%1$s" did not create a valid module', [$filePath])
            );
        }

        return $module;
    }
}
