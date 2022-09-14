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

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

/**
 *  WooCommerce Settings Fields
 */
final class SettingView
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var MetaboxField[]
     */
    private $fields;

    /**
     * Setting constructor.
     * @param string $name
     * @param MetaboxField ...$fields
     */
    public function __construct(string $name, MetaboxField ...$fields)
    {
        $this->name = $name;
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        ?>
        <div id="<?= esc_attr($this->id($relationshipContext)) ?>"
             class="panel woocommerce_options_panel"
        >
            <?php array_walk(
                $this->fields,
                static function (MetaboxField $field) use ($helper, $relationshipContext) {
                    $field->render($helper, $relationshipContext);
                }
            ); ?>
        </div>
        <?php
    }

    /**
     * Retrieve the setting container attribute id.
     *
     * @param RelationshipContext $relationshipContext
     * @return string
     */
    private function id(RelationshipContext $relationshipContext): string
    {
        return sprintf(
            'mlp_%1$s_%2$s_product_data',
            $relationshipContext->remoteSiteId(),
            $this->name
        );
    }
}
