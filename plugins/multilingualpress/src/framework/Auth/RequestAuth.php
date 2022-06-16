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

namespace Inpsyde\MultilingualPress\Framework\Auth;

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

/**
 * Class RequestAuth
 * @package Inpsyde\MultilingualPress\Framework\Http\Auth
 */
final class RequestAuth implements Auth
{
    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var string
     */
    private $capability;

    /**
     * Validator constructor
     * @param Nonce $nonce
     * @param $capability
     */
    public function __construct(Nonce $nonce, Capability $capability)
    {
        $this->nonce = $nonce;
        $this->capability = $capability;
    }

    /**
     * @inheritDoc
     */
    public function isAuthorized(): bool
    {
        return $this->nonce->isValid() && $this->capability->isValid();
    }
}
