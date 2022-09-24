<?php

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\Blocks\Context;

use RuntimeException;

/**
 * Can create a context from the given attributes.
 *
 * @psalm-type name = string
 * @psalm-type value = scalar|array
 */
interface ContextFactoryInterface
{
    /**
     * Creates the context from the given attributes.
     *
     * @param array<string, mixed> $attributes A map of attribute name to value.
     * @psalm-param array<name, value> $attributes
     * @return array<string, mixed> The context.
     * @psalm-return array<name, value>
     * @throws RuntimeException If problem creating.
     */
    public function createContext(array $attributes): array;
}
