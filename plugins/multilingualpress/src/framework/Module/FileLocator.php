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

use CallbackFilterIterator;
use Exception;
use Iterator;
use IteratorAggregate;
use OutOfRangeException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Traversable;
use UnexpectedValueException;

/**
 * When pointed to a directory of modules, locates module files in that directory.
 */
class FileLocator implements IteratorAggregate
{
    /**
     * The base directory to look for files in.
     *
     * @var string
     */
    protected $baseDir;

    /**
     * The name of the module file.
     *
     * @var string
     */
    protected $moduleFileName;

    /**
     * The maximal directory depth to scan into.
     *
     * @var int
     */
    protected $maxDepth;

    public function __construct(string $baseDir, string $moduleFileName, int $maxDepth)
    {
        $this->baseDir = $baseDir;
        $this->moduleFileName = $moduleFileName;
        $this->maxDepth = $maxDepth;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception If problem retrieving internal iterator.
     * {@see https://youtrack.jetbrains.com/issue/WI-44884}.
     */
    public function getIterator()
    {
        return $this->getPaths();
    }

    /**
     * Retrieves paths of module files.
     *
     * @throws Exception If problem retrieving paths.
     *
     * @return Traversable The list of file name paths.
     */
    protected function getPaths(): Traversable
    {
        $dir = $this->baseDir;
        $directories = $this->createRecursiveDirectoryIterator($dir);
        $directories->setMaxDepth($this->maxDepth);
        $paths = $this->filterList(
            $directories,
            function (SplFileInfo $current, string $key, Iterator $iterator): bool {
                return $this->filterFile($current);
            }
        );

        return $paths;
    }

    /**
     * Determines whether or not a module file is valid.
     *
     * @param SplFileInfo $fileInfo The file to filter.
     *
     * @return bool True if the file is valid for inclusion; false otherwise.
     */
    protected function filterFile(SplFileInfo $fileInfo): bool
    {
        if (!$fileInfo->isFile()) {
            return false;
        }
        if ($fileInfo->getBasename() !== $this->moduleFileName) {
            return false;
        }

        return true;
    }

    /**
     * Creates a recursive directory iterator.
     *
     * @param string $dir Path to the directory to iterate over.
     *
     * @throws UnexpectedValueException If the directory cannot be accessed.
     *
     * @return RecursiveIteratorIterator The iterator that will recursively
     * iterate over items in the specified directory.
     */
    protected function createRecursiveDirectoryIterator(string $dir): RecursiveIteratorIterator
    {
        $directories = new RecursiveDirectoryIterator($dir);
        $flattened = new RecursiveIteratorIterator($directories);

        return $flattened;
    }

    /**
     * Filters a list of items by applying a callback.
     *
     * @param Traversable $list The list to filter.
     * @param callable $callback The callback criteria to use for filtering.
     *
     * @return Traversable The list of items from the iterator that match the criteria.
     */
    protected function filterList(Traversable $list, callable $callback): Traversable
    {
        $list = $this->resolveIterator($list);

        return new CallbackFilterIterator($list, $callback);
    }

    /**
     * Finds the deepest iterator that matches.
     *
     * Because the given traversable can be an {@see IteratorAggregate},
     * it will try to get its inner iterator.
     *
     * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
     * @link https://github.com/Dhii/iterator-helper-base/blob/v0.1-alpha2/src/ResolveIteratorCapableTrait.php
     * phpcs:enable
     * Ported from here.
     *
     * @param Traversable $iterator The iterator to resolve.
     * @param int         $limit    The depth limit for resolution.
     *
     * @throws OutOfRangeException      If infinite recursion is detected.
     * @throws UnexpectedValueException If the iterator could not be resolved within
     *                                  the depth limit.
     *
     * @return Iterator The inner-most iterator, or whatever the test function allows.
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    protected function resolveIterator(Traversable $iterator, int $limit = 100): Iterator
    {
        $i = 0;
        while ($i < $limit) {
            if ($iterator instanceof IteratorAggregate) {
                $tempIterator = $iterator->getIterator();
                if ($iterator === $tempIterator) {
                    throw new OutOfRangeException(
                        vsprintf('Infinite recursion: looks like the traversable wraps itself on level %1$d', [$i])
                    );
                }
                $iterator = $tempIterator;
                ++$i;
                continue;
            }

            break; // Found `Iterator`
        }
        // phpcs:enable

        if (!$iterator instanceof Iterator) {
            throw new UnexpectedValueException(
                vsprintf('The deepest iterator is not a match (limit is %1$d)', [$limit])
            );
        }
        return $iterator;
    }
}
