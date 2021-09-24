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

namespace Inpsyde\MultilingualPress\TranslationUi\Post;

use Inpsyde\MultilingualPress\Framework\Filter\Filter;
use Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;

use function Inpsyde\MultilingualPress\wpHookProxy;

class PostModifiedDateFilter implements Filter
{
    use FilterTrait;

    public function __construct()
    {
        $this->acceptedArgs = 2;
        $this->callback = wpHookProxy([$this, 'doNotUpdateModifiedDate']);
        $this->hook = 'wp_insert_post_data';
    }

    /**
     * @param array $data
     * @param array $postarr
     * @return array
     */
    public function doNotUpdateModifiedDate(array $data, array $postarr): array
    {
        $post = get_post($postarr['ID']);
        if (!$post) {
            return $data;
        }

        $data['post_modified'] = $post->post_modified;
        $data['post_modified_gmt'] = $post->post_modified_gmt;

        return $data;
    }
}
