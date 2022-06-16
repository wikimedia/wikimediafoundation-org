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

namespace Inpsyde\MultilingualPress\TranslationUi\Post;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

/**
 * Interface for any kind of custom Metabox tabs.
 */
interface MetaboxFillable
{
    /**
     * The id of the metabox tab.
     *
     * @return string
     */
    public function id(): string;

    /**
     * The label to show to the tab header.
     *
     * @return string
     */
    public function label(): string;

    /**
     * The fields collection for the current tab.
     *
     * @return MetaboxField[]
     */
    public function fields(): array;

    /**
     * If the metabox tab is enabled or not.
     *
     * @param RelationshipContext $relationshipContext
     * @return bool
     */
    public function enabled(RelationshipContext $relationshipContext): bool;

    /**
     * Render the metabox markup.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     */
    public function render(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext);
}
