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

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

interface PostMetaboxField
{
    /**
     * @return string
     */
    public function key();

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     */
    public function render(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext);

    /**
     * @param Request $request
     * @param MetaboxFieldsHelper $helper
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function requestValue(Request $request, MetaboxFieldsHelper $helper);

    /**
     * @param RelationshipContext $relationshipContext
     * @return bool
     */
    public function enabled(RelationshipContext $relationshipContext): bool;
}
