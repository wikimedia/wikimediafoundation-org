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

namespace Inpsyde\MultilingualPress\Framework\Admin\Metabox;

interface PostMetabox extends Metabox
{
    /**
     * @param \WP_Post $post
     * @param string $saveOrShow
     * @return bool
     */
    public function acceptPost(\WP_Post $post, string $saveOrShow): bool;

    /**
     * @param \WP_Post $post
     * @return View
     */
    public function viewForPost(\WP_Post $post): View;

    /**
     * @param \WP_Post $post
     * @return Action
     */
    public function actionForPost(\WP_Post $post): Action;
}
