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

namespace Inpsyde\MultilingualPress\Framework\Admin\Metabox;

use Inpsyde\MultilingualPress\Framework\Entity;

/**
 * @package MultilingualPress
 * @license http://opensource.org/licenses/MIT MIT
 */
interface Metabox
{
    const SAVE = 'save';
    const SHOW = 'show';

    /**
     * @param string $showOrSave
     * @param Entity $entity
     * @return Info
     */
    public function createInfo(string $showOrSave, Entity $entity): Info;

    /**
     * Returns the site ID for the meta box.
     * @return int
     */
    public function siteId(): int;
}
