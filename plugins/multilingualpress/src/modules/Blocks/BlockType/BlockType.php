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

namespace Inpsyde\MultilingualPress\Module\Blocks\BlockType;

use Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface;
use Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface;

class BlockType implements BlockTypeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $extra;

    /**
     * @var string
     */
    protected $templatePath;

    /**
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @var TemplateRendererInterface
     */
    protected $templateRenderer;

    public function __construct(
        string $name,
        string $category,
        string $icon,
        string $title,
        string $description,
        array $attributes,
        array $extra,
        string $templatePath,
        ContextFactoryInterface $contextFactory,
        TemplateRendererInterface $templateRenderer
    ) {

        $this->name = $name;
        $this->category = $category;
        $this->attributes = $attributes;
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
        $this->extra = $extra;
        $this->contextFactory = $contextFactory;
        $this->templateRenderer = $templateRenderer;
        $this->templatePath = $templatePath;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function category(): string
    {
        return $this->category;
    }

    /**
     * @inheritDoc
     */
    public function icon(): string
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function extra(): array
    {
        return $this->extra;
    }

    /**
     * @inheritDoc
     */
    public function contextFactory(): ContextFactoryInterface
    {
        return $this->contextFactory;
    }

    /**
     * @inheritDoc
     */
    public function templatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * @inheritDoc
     */
    public function render(array $attributes): string
    {
        $context = $this->contextFactory()->createContext($attributes);

        return $this->templateRenderer->render($this->templatePath(), $context);
    }
}
