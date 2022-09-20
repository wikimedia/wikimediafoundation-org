<?php

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\Blocks\BlockType;

use RuntimeException;

/**
 * Can create a BlockType.
 *
 * @psalm-type optionName = string
 * @psalm-type optionValue = array{type: string}
 * @psalm-type extraValue = scalar|array
 * @psalm-type blockConfig = array{
 *      name: string,
 *      category: string,
 *      attributes: array<optionName, optionValue>,
 *      templatePath: string,
 *      contextFactory: ContextFactoryInterface,
 *      icon?: string,
 *      title?: string,
 *      description?: string,
 *      extra?: array<optionName, extraValue>,
 * }
 */
interface BlockTypeFactoryInterface
{
    /**
     * Creates a new block type instance with a given config.
     *
     * @param array $config The config.
     * @psalm-param blockConfig $config
     * @return BlockTypeInterface The new instance.
     * @throws RuntimeException If problem creating.
     */
    public function createBlockType(array $config): BlockTypeInterface;
}
