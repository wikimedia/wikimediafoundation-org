<?php

namespace Inpsyde\MultilingualPress\Module\Blocks\BlockType;

use Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface;

class BlockTypeFactory implements BlockTypeFactoryInterface
{
    /**
     * @var TemplateRendererInterface
     */
    protected $templateRenderer;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * @inheritDoc
     */
    public function createBlockType(array $config): BlockTypeInterface
    {
        return new BlockType(
            $config['name'],
            $config['category'],
            $config['icon'] ?? '',
            $config['title'] ?? '',
            $config['description'] ?? '',
            $config['attributes'],
            $config['extra'] ?? [],
            $config['templatePath'] ?? '',
            $config['contextFactory'],
            $this->templateRenderer
        );
    }
}
