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

namespace Inpsyde\MultilingualPress\Module\User\TranslationUi;

class MetaboxFields
{
    const FIELD_BIOGRAPHY = 'description';

    /**
     * Will return array of all user translatable fields
     *
     * @return array of All user translatable fields
     */
    public function allFields(): array
    {
        return [
            self::FIELD_BIOGRAPHY => new Field\Biography(self::FIELD_BIOGRAPHY),
        ];
    }
}
