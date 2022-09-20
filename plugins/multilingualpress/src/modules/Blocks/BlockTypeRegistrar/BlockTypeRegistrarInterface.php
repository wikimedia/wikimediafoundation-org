<?php

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar;

use Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface;
use RuntimeException;

/**
 * Can register a BlockType.
 */
interface BlockTypeRegistrarInterface
{
    /**
     * Register the given block type.
     *
     * @param BlockTypeInterface $blockType The block type to register.
     * @throws RuntimeException If failing to register.
     */
    public function register(BlockTypeInterface $blockType): void;
}
