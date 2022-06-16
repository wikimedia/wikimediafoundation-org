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

namespace Inpsyde\MultilingualPress\Framework\Message;

/**
 * Class Message
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
class Message implements MessageInterface
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $type;

    /**
     * SuccessMessage constructor.
     * @param string $type
     * @param string $content
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(string $type, string $content, array $data)
    {
        if (!$type) {
            throw new \InvalidArgumentException('Message type cannot be empty.');
        }

        if ($content === '') {
            throw new \InvalidArgumentException('Message content cannot be empty.');
        }

        $this->type = $type;
        $this->content = $content;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function isOfType(string $type): bool
    {
        return $this->type() === $type;
    }
}
