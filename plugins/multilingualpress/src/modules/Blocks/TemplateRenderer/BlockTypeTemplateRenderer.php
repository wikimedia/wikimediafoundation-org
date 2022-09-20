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

namespace Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer;

use RuntimeException;

class BlockTypeTemplateRenderer implements TemplateRendererInterface
{
    /**
     * @inheritDoc
     * phpcs:disable Inpsyde.CodeQuality.StaticClosure
     */
    public function render(string $templatePath, array $context): string
    {
        if (!is_readable($templatePath)) {
            throw new RuntimeException("Template Not Found: {$templatePath}");
        }

        ob_start();
        $template = function (array $context) use ($templatePath) {
            /** @noinspection PhpIncludeInspection */
            require $templatePath;
        };
        // phpcs:enable

        $template($context);

        return (string)ob_get_clean();
    }
}
