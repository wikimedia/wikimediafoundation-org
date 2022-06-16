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

/**
 * Class ModelCollectionValidator
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
 */
trait ModelCollectionValidator
{
    /**
     * Check that all of the items withing the give argument are instances of
     * ModelInterface
     *
     * @param array $models
     * @return bool
     */
    protected function validate(array $models): bool
    {
        if (empty($models)) {
            return true;
        }

        foreach ($models as $model) {
            if (!$model instanceof ModelInterface) {
                return false;
            }
        }

        return true;
    }
}
