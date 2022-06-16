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

namespace Inpsyde\MultilingualPress\Module\QuickLinks\Model;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Class Collection
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
 */
class Collection implements IteratorAggregate, Countable
{
    use ModelCollectionValidator;

    /**
     * @var ModelInterface[]
     */
    private $collection;

    /**
     * Collection constructor.
     * @param array $models
     * @throws InvalidArgumentException
     */
    public function __construct(array $models)
    {
        if (!$this->validate($models)) {
            throw new InvalidArgumentException(
                'All elements within the given array must be an instance of ModelInterface.'
            );
        }

        $this->collection = $models;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->collection);
    }
}
