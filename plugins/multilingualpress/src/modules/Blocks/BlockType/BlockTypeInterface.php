<?php

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\Blocks\BlockType;

use Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface;
use RuntimeException;

/**
 * Represents the BlockType.
 *
 * @psalm-type name = string
 * @psalm-type type = array{type: string}
 * @psalm-type value = scalar|array
 */
interface BlockTypeInterface
{
    /**
     * The name of the block type.
     *
     * @return string
     */
    public function name(): string;

    /**
     * The block type category name, used in search interfaces to arrange block types by category.
     *
     * @return string
     */
    public function category(): string;

    /**
     * The block type icon.
     *
     * @return string
     */
    public function icon(): string;

    /**
     * The block type title.
     *
     * @return string
     */
    public function title(): string;

    /**
     * The block type description.
     *
     * @return string
     */
    public function description(): string;


    /**
     * Returns block type attributes config.
     *
     * @return array<string, mixed> A map of attribute name to type.
     * @psalm-return array<name, type>
     */
    public function attributes(): array;

    /**
     * Returns block extra config.
     *
     * These are additional custom configs which can contain block type specific information.
     *
     * @return array<string, mixed> A map of extra config name to value.
     * @psalm-return array<name, value>
     */
    public function extra(): array;

    /**
     * Renders the block type with given attributes.
     *
     * @param array<string, mixed> $attributes A map of attribute name to value.
     * @psalm-param array<name, value> $attributes
     * @return string
     * @throws RuntimeException If problem rendering.
     */
    public function render(array $attributes): string;

    /**
     * The context factory.
     *
     * @return ContextFactoryInterface
     */
    public function contextFactory(): ContextFactoryInterface;

    /**
     * Returns the template path of a block type.
     *
     * @return string The template path.
     */
    public function templatePath(): string;
}
