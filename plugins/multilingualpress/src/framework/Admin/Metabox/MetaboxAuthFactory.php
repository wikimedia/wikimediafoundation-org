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

namespace Inpsyde\MultilingualPress\Framework\Admin\Metabox;

use Inpsyde\MultilingualPress\Framework\Auth\Auth;
use Inpsyde\MultilingualPress\Framework\Auth\AuthFactoryException;
use Inpsyde\MultilingualPress\Framework\Auth\EntityAuthFactory;
use Inpsyde\MultilingualPress\Framework\Entity;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce;

use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Class MetaboxAuthFactory
 * @package Inpsyde\MultilingualPress\Framework\Admin\Metabox
 */
class MetaboxAuthFactory
{
    /**
     * @var EntityAuthFactory
     */
    private $entityAuthFactory;

    /**
     * @var NonceFactory
     */
    private $nonceFactory;

    /**
     * MetaboxAuthFactory constructor.
     * @param EntityAuthFactory $entityAuthFactory
     * @param NonceFactory $nonceFactory
     */
    public function __construct(EntityAuthFactory $entityAuthFactory, NonceFactory $nonceFactory)
    {
        $this->entityAuthFactory = $entityAuthFactory;
        $this->nonceFactory = $nonceFactory;
    }

    /**
     * Create an instance of Auth by the given Entity
     *
     * @param Entity $entity
     * @param string $metaboxId
     * @param int $metaboxSiteId
     * @return Auth
     * @throws AuthFactoryException
     */
    public function create(Entity $entity, string $metaboxId, int $metaboxSiteId): Auth
    {
        /** @var SiteAwareNonce $nonce */
        $nonce = $this->nonceFactory->create(["{$metaboxId}-{$entity->id()}"]);

        try {
            $auth = $this->entityAuthFactory->create($entity, $nonce->withSite($metaboxSiteId));
        } catch (AuthFactoryException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }

        return $auth;
    }
}
