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

use Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag;
use Inpsyde\MultilingualPress\Framework\Url\Url;

/**
 * Interface ModelInterface
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
 */
interface ModelInterface
{
    /**
     * Return the Url
     *
     * @return Url
     */
    public function url(): Url;

    /**
     * Return the Language HTTP Code
     *
     * @return Bcp47Tag
     */
    public function language(): Bcp47Tag;

    /**
     * Return a Text Label
     *
     * @return string
     */
    public function label(): string;
}
