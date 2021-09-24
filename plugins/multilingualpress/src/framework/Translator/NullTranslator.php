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

namespace Inpsyde\MultilingualPress\Framework\Translator;

use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;

/**
 * Null translator implementation.
 */
final class NullTranslator implements Translator
{

    /**
     * @inheritdoc
     */
    public function translationFor(int $siteId, TranslationSearchArgs $args): Translation
    {
        return new Translation();
    }
}
