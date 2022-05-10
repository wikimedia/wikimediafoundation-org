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

use Inpsyde\MultilingualPress\Framework\Nonce\Exception\ContextValueManipulationNotAllowed;

trait ReadOnlyContextTrait
{

    /**
     * @param $name
     * @param $value
     * @throws ContextValueManipulationNotAllowed
     */
    public function offsetSet($name, $value)
    {
        throw ContextValueManipulationNotAllowed::forName($name, 'set');
    }

    /**
     * @param $name
     * @throws ContextValueManipulationNotAllowed
     */
    public function offsetUnset($name)
    {
        throw ContextValueManipulationNotAllowed::forName($name, 'unset');
    }
}
