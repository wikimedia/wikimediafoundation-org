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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

/**
 * MultilingualPress WooCommerce Metabox Field
 *
 * This class is a proxy to Post\MetaboxField
 */
final class MetaboxField implements WooCommerceMetaboxField
{
    /**
     * @var PostMetaboxField
     */
    private $metaboxField;

    /**
     * MetaboxField constructor.
     * @param PostMetaboxField $metaboxField
     */
    public function __construct(PostMetaboxField $metaboxField)
    {
        $this->metaboxField = $metaboxField;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->metaboxField->key();
    }

    /**
     * @inheritdoc
     */
    public function render(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $this->metaboxField->render($helper, $relationshipContext);
    }

    /**
     * @inheritdoc
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function requestValue(Request $request, MetaboxFieldsHelper $helper)
    {
        return $this->metaboxField->requestValue($request, $helper);
    }

    /**
     * @inheritdoc
     */
    public function enabled(RelationshipContext $relationshipContext): bool
    {
        return $this->metaboxField->enabled($relationshipContext);
    }
}
