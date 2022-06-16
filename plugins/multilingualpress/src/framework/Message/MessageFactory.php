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

use OutOfRangeException;
use UnexpectedValueException;

/**
 * A factory of Messages.
 *
 * Will create instances of a class that corresponds to the message type,
 * or fall back to a default class if specified, finally throwing.
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * @var array
     */
    private $typeFactories;

    /**
     * @var string|null
     */
    private $fallbackFactory;

    /**
     * @param array<string, callable> $typeFactories
     * A map of message type codes to their respective factories.
     * Each factory has the following signature:
     * `function (string $code, string $content, array $data)`
     * @param callable|null $fallbackFactory The factory of the class to use if type code doesn't match.
     * Use `null` to indicated that no fallback should be used, and the factory should throw instead.
     */
    public function __construct(array $typeFactories, callable $fallbackFactory = null)
    {
        $this->typeFactories = $typeFactories;
        $this->fallbackFactory = $fallbackFactory;
    }

    /**
     * @inheritDoc
     */
    public function create(string $type, string $content, array $data): MessageInterface
    {
        $factory = $this->messageFactory($type);
        if (!\is_callable($factory)) {
            throw new UnexpectedValueException(
                sprintf('Could not invoke message factory for type "%1$s"', $type)
            );
        }

        $message = $factory($type, $content, $data);

        return $message;
    }

    /**
     * Retrieves a factory for a message type.
     *
     * @param string $type The code of a message type.
     *
     * @return callable The factory of a message. See {@see __construct()} parameter `$typeFactories`.
     * @throws OutOfRangeException If specified type code is invalid.
     *
     */
    protected function messageFactory(string $type): callable
    {
        if (isset($this->typeFactories[$type])) {
            return $this->typeFactories[$type];
        }

        if (isset($this->fallbackFactory)) {
            return $this->fallbackFactory;
        }

        throw new OutOfRangeException(sprintf('Type code "%1$s" is invalid', $type));
    }
}
