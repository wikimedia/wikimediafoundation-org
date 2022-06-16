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

use Inpsyde\MultilingualPress\Framework\Entity;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

/**
 * Class EntityAuthFactory
 * @package Inpsyde\MultilingualPress\Framework\Http\Auth
 */
class EntityAuthFactory
{
    /**
     * @param Entity $entity
     * @param Nonce $nonce
     * @return Auth
     * @throws AuthFactoryException
     */
    public function create(Entity $entity, Nonce $nonce): Auth
    {
        $capability = null;

        if (!$entity->isValid()) {
            throw AuthFactoryException::becauseEntityIsInvalid();
        }

        switch ($entity->type()) {
            case 'WP_Post':
                $auth = new PostAuth($entity->expose(), $nonce);
                break;
            case 'WP_Term':
                $auth = new TermAuth($entity->expose(), $nonce);
                break;
        }

        return $auth;
    }
}
