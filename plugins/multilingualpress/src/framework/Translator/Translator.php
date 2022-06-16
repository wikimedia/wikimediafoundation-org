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

namespace Inpsyde\MultilingualPress\Framework\Translator;

use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;

/**
 * Interface for all translator implementations.
 */
interface Translator
{
    /**
     * Returns the translation data for the given site, according to the given arguments.
     *
     * @param int $siteId
     * @param TranslationSearchArgs $args
     * @return Translation
     */
    public function translationFor(int $siteId, TranslationSearchArgs $args): Translation;
}
