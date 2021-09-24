<?php

/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\MultilingualPress\Framework\Message;

/**
 * Represents a factory of messages.
 */
interface MessageFactoryInterface
{
    /**
     * Create a new instance of a Message.
     *
     * @param string $type The message type. This is determined by module semantics.
     * @param string $content The message content.
     * @param array $data Structured data of the message.
     * All values in this array must be recursively serializable
     * @return MessageInterface The new message.
     *
     * @throws \InvalidArgumentException If message data is invalid.
     * @throws \Exception If message could not be created.
     */
    public function create(string $type, string $content, array $data): MessageInterface;
}
