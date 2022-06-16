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

namespace Inpsyde\MultilingualPress\Framework\Setting\User;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

/**
 * User setting updater implementation validating a nonce specific to the update action included in
 * the request data.
 */
class UserSettingUpdater
{

    /**
     * @var string
     */
    private $metaKey;

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param string $metaKey
     * @param Request $request
     * @param Nonce|null $nonce
     */
    public function __construct(string $metaKey, Request $request, Nonce $nonce = null)
    {
        $this->metaKey = $metaKey;
        $this->nonce = $nonce;
        $this->request = $request;
    }

    /**
     * Updates the setting with the data in the request for the user with the given ID.
     *
     * @param int $userId
     * @return bool
     */
    public function update(int $userId): bool
    {
        if (!current_user_can('edit_user', $userId)) {
            return false;
        }

        if ($this->nonce && !$this->nonce->isValid()) {
            return false;
        }

        $value = $this->request->bodyValue(
            $this->metaKey,
            INPUT_REQUEST,
            FILTER_SANITIZE_STRING
        );

        if (!is_string($value)) {
            $value = '';
        }

        return $value
            ? (bool)update_user_meta($userId, $this->metaKey, $value)
            : delete_user_meta($userId, $this->metaKey);
    }
}
