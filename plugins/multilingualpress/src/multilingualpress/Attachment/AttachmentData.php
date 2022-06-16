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

namespace Inpsyde\MultilingualPress\Attachment;

/**
 * Class AttachmentData
 */
class AttachmentData
{
    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var array
     */
    private $meta;

    /**
     * @var string
     */
    private $filePath;

    /**
     * AttachmentData constructor.
     * @param \WP_Post $post
     * @param array $meta
     * @param string $filePath
     */
    public function __construct(\WP_Post $post, array $meta, string $filePath)
    {
        $this->post = $post;
        $this->meta = $meta;
        $this->filePath = $filePath;
    }

    /**
     * @return \WP_Post
     */
    public function post(): \WP_Post
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * @return string
     */
    public function filePath(): string
    {
        return $this->filePath;
    }
}
