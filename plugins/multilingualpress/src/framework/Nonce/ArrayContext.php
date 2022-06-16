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

namespace Inpsyde\MultilingualPress\Framework\Nonce;

use Inpsyde\MultilingualPress\Framework\Nonce\Exception\ContextValueNotSet;

/**
 * Array-based nonce context implementation.
 */
final class ArrayContext implements Context
{
    use ReadOnlyContextTrait;

    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!is_array($this->data)) {
            $this->data = $data;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($name): bool
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        }

        throw ContextValueNotSet::forName($offset, 'read');
    }
}
