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

use DomainException;
use Inpsyde\MultilingualPress\Framework\Admin\AdminNotice;
use Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices;
use Inpsyde\MultilingualPress\Framework\Auth\AuthFactoryException;
use Inpsyde\MultilingualPress\Framework\Entity;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use WP_Post;
use WP_Term;

use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Class MetaboxUpdater
 * @package Inpsyde\MultilingualPress\Framework\Admin\Metabox
 */
class MetaboxUpdater
{
    use SwitchSiteTrait;

    const ACTION_UNAUTHORIZED_METABOX_SAVE = 'multilingualpress.unauthorized_box_save';
    const ACTION_SAVE_METABOX = 'multilingualpress.save_metabox';
    const ACTION_SAVED_METABOX = 'multilingualpress.saved_metabox';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var PersistentAdminNotices
     */
    private $persistentAdminNotice;

    /**
     * @var MetaboxAuthFactory
     */
    private $metaboxAuthFactory;

    /**
     * MetaboxUpdater constructor.
     * @param Request $request
     * @param PersistentAdminNotices $notices
     * @param MetaboxAuthFactory $metaboxAuthFactory
     */
    public function __construct(
        Request $request,
        PersistentAdminNotices $notices,
        MetaboxAuthFactory $metaboxAuthFactory
    ) {

        $this->request = $request;
        $this->persistentAdminNotice = $notices;
        $this->metaboxAuthFactory = $metaboxAuthFactory;
    }

    /**
     * Save Metabox Data for the given Entity
     *
     * @param Metabox|PostMetabox|TermMetabox $metabox
     * @param string $metaboxId
     * @param Entity $entity
     * @throws AuthFactoryException
     * @throws DomainException
     */
    public function saveMetabox(Metabox $metabox, string $metaboxId, Entity $entity)
    {
        if (!$entity->isValid()) {
            return;
        }

        $metaboxSiteId = $metabox->siteId();

        $auth = $this->metaboxAuthFactory->create(
            $entity,
            $metaboxId,
            $metaboxSiteId
        );

        if (!$auth->isAuthorized()) {
            /**
             * Action for Unauthorized Metabox Save
             *
             * @params Metabox $box
             * @params string $metaboxId
             * @params Entity $entity
             */
            do_action(self::ACTION_UNAUTHORIZED_METABOX_SAVE, $metabox, $metaboxId, $entity);

            return;
        }

        $previousSiteId = $this->maybeSwitchSite($metaboxSiteId);

        /**
         * Action Save Metabox
         *
         * @param Metabox $metabox
         * @param Entity $entity
         */
        do_action(self::ACTION_SAVE_METABOX, $metabox, $entity);

        try {
            $action = $this->actionFactory($entity, $metabox);
        } catch (DomainException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
            return;
        }
        $action->save($this->request, $this->persistentAdminNotice);

        /**
         * Action Saved Metabox
         *
         * @param Metabox $metabox
         * @param Entity $entity
         * @param Request $request
         * @param AdminNotice $notice
         */
        do_action(self::ACTION_SAVED_METABOX, $metabox, $entity);

        $this->maybeRestoreSite($previousSiteId);
    }

    /**
     * Create instance of Action by the given metabox for the given Entity
     *
     * @param Entity $entity
     * @param Metabox|PostMetabox|TermMetabox $metabox
     * @return Action
     * @throws DomainException
     */
    protected function actionFactory(Entity $entity, Metabox $metabox): Action
    {
        if (!$entity->isValid()) {
            throw new DomainException(
                'Entity is not valid. Cannot create action based on entity.'
            );
        }

        switch ($entity->type()) {
            case WP_Post::class:
                $action = $metabox->actionForPost($entity->expose());
                break;
            case WP_Term::class:
                $action = $metabox->actionForTerm($entity->expose());
                break;
        }

        return $action;
    }
}
