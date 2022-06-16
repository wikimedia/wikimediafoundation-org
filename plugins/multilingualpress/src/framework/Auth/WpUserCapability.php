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

use WP_User;

/**
 * Class WpUserCapability
 * @package Inpsyde\MultilingualPress\Framework\Http\Auth
 */
class WpUserCapability implements Capability
{
    /**
     * @var WP_User
     */
    private $user;

    /**
     * @var string
     */
    private $capability;

    /**
     * @var int
     */
    private $id;

    /**
     * WpCurrentUserCapability constructor
     * @param WP_User $user
     * @param string $capability
     * @param int $id
     */
    public function __construct(WP_User $user, string $capability, int $id)
    {
        $this->user = $user;
        $this->capability = $capability;
        $this->id = $id;
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return $this->id > 0
            ? $this->user->has_cap($this->capability, $this->id)
            : $this->user->has_cap($this->capability);
    }
}
