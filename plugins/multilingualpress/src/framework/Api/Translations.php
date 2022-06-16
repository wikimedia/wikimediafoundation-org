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

namespace Inpsyde\MultilingualPress\Framework\Api;

use Inpsyde\MultilingualPress\Framework\Translator\Translator;

/**
 * Interface for all translations API implementations.
 */
interface Translations
{
    /**
     * Returns all translations according to the given arguments.
     *
     * @param TranslationSearchArgs $args
     * @return Translation[]
     */
    public function searchTranslations(TranslationSearchArgs $args): array;

    /**
     * Registers the given translator for the given type.
     *
     * @param Translator $translator
     * @param string $type
     * @return bool
     */
    public function registerTranslator(Translator $translator, string $type): bool;
}
