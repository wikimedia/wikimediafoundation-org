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

namespace Inpsyde\MultilingualPress\Schedule\Action;

use Inpsyde\MultilingualPress\Framework\Nonce\Context;
use Inpsyde\MultilingualPress\Framework\Nonce\WpNonce;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait AuthorizationTrait
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
trait AuthorizationTrait
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var WpNonce|MockObject
     */
    private $nonce;

    /**
     * @var string
     */
    private $userCapability;

    /**
     * @return bool
     */
    protected function isUserAuthorized(): bool
    {
        return $this->nonce->isValid($this->context)
            && current_user_can($this->userCapability);
    }
}
