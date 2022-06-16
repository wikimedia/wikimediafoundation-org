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

namespace Inpsyde\MultilingualPress\Framework\Nonce;

use Inpsyde\MultilingualPress\Framework\Http\PhpServerRequest;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Nonce\Exception\ContextValueNotSet;

/**
 * Nonce context implementation wrapping around the server request.
 */
final class ServerRequestContext implements Context
{
    use ReadOnlyContextTrait;

    /**
     * @var ServerRequest
     */
    private $request;

    /**
     * @param ServerRequest|null $request
     */
    public function __construct(ServerRequest $request = null)
    {
        $this->request = $request ?? new PhpServerRequest();
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($name): bool
    {
        return $this->request->bodyValue($name) !== null;
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->request->bodyValue($offset);
        }

        throw ContextValueNotSet::forName($offset, 'read');
    }
}
