<?php

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer;

use RuntimeException;

/**
 * Can render the given template with the given context.
 */
interface TemplateRendererInterface
{
    /**
     * Renders the given template with the given context.
     *
     * @param string $templatePath The template path.
     * @param array<string, mixed> $context The context.
     * @return string The rendered HTML.
     * @throws RuntimeException If failing to render.
     */
    public function render(string $templatePath, array $context): string;
}
