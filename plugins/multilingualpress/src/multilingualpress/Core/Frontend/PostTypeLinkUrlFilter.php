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

namespace Inpsyde\MultilingualPress\Core\Frontend;

use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Framework\Filter\Filter;
use Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;

use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Post type link URL filter.
 */
final class PostTypeLinkUrlFilter implements Filter
{
    use FilterTrait;

    /**
     * @var PostTypeRepository
     */
    private $postTypeRepository;

    /**
     * @param PostTypeRepository $postTypeRepository
     */
    public function __construct(PostTypeRepository $postTypeRepository)
    {
        $this->postTypeRepository = $postTypeRepository;
        $this->acceptedArgs = 2;
        $this->callback = wpHookProxy([$this, 'unprettifyPermalink']);
        $this->hook = 'post_type_link';
    }

    /**
     * Filters the post type link URL and returns a query-based representation, if set for the
     * according post type.
     *
     * @param string $postLink
     * @param \WP_Post $post
     * @return string
     */
    public function unprettifyPermalink(
        string $postLink,
        \WP_Post $post
    ): string {

        if (!$this->postTypeRepository->isPostTypeQueryBased($post->post_type)) {
            return $postLink;
        }

        $postType = get_post_type_object($post->post_type);
        if (!$postType instanceof \WP_Post_Type) {
            return $postLink;
        }

        $args = ['p' => $post->ID];
        if ($postType->query_var && !$this->isDraftOrPending($post)) {
            $args = [$postType->query_var => $post->post_name];
        }

        return (string)home_url(add_query_arg($args, ''));
    }

    /**
     * Checks if the given post is a draft or pending.
     *
     * @param \WP_Post $post
     * @return bool
     */
    private function isDraftOrPending(\WP_Post $post): bool
    {
        if (empty($post->post_status)) {
            return false;
        }

        return in_array($post->post_status, ['draft', 'pending', 'auto-draft'], true);
    }
}
