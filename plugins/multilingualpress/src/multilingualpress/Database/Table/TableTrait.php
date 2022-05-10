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

namespace Inpsyde\MultilingualPress\Database\Table;

use function Inpsyde\MultilingualPress\tableExists;

/**
 * Trait TableTrait
 *
 * @see Table
 */
trait TableTrait
{
    /**
     * @inheritdoc
     */
    public function exists(): bool
    {
        return tableExists($this->name());
    }
}
