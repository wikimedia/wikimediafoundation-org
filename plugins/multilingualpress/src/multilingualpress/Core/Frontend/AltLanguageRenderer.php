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

namespace Inpsyde\MultilingualPress\Core\Frontend;

/**
 * Interface for all alternate language renderer implementations.
 */
interface AltLanguageRenderer
{
    const TYPE_HTTP_HEADER = 1;
    const TYPE_HTML_LINK_TAG = 2;

    /**
     * Returns the output type.
     *
     * @return int
     */
    public function type(): int;

    /**
     * Renders all available alternate languages.
     *
     * @param array ...$args
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function render(...$args);
}
