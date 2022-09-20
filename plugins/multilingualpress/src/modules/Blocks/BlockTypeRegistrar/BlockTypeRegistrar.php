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

namespace Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar;

use Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface;
use RuntimeException;

class BlockTypeRegistrar implements BlockTypeRegistrarInterface
{
    /**
     * @var string
     */
    protected $scriptName;

    public function __construct(string $scriptName)
    {
        $this->scriptName = $scriptName;
    }

    /**
     * @inheritDoc
     */
    public function register(BlockTypeInterface $blockType): void
    {
        $registered = register_block_type(
            $blockType->name(),
            [
                'render_callback' => static function (array $attributes) use ($blockType): string {
                    return $blockType->render($attributes);
                },
                'attributes' => $blockType->attributes(),
            ]
        );

        if (!$registered) {
            throw new RuntimeException("Couldn't register the block");
        }

        wp_localize_script(
            $this->scriptName,
            $this->blockNameAsVariableName($blockType->name()),
            [
                'name' => $blockType->name(),
                'title' => $blockType->title(),
                'description' => $blockType->description(),
                'icon' => $blockType->icon(),
                'category' => $blockType->category(),
                'attributes' => $blockType->attributes(),
                'extra' => $blockType->extra(),
            ]
        );
    }

    /**
     * Converts the given block name to JS variable name.
     *
     * @param string $blockName The name of the block.
     * @return string The converted JS variable name.
     */
    protected function blockNameAsVariableName(string $blockName): string
    {
        $cleanBlockName = str_replace(['_', '-'], '/', $blockName);
        $parts = \array_map('ucwords', \explode('/', $cleanBlockName));
        return \lcfirst(\implode('', $parts));
    }
}
