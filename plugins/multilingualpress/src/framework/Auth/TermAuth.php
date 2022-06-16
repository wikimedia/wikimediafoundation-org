<?php

declare(strict_types=1);

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\MultilingualPress\Framework\Auth;

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use WP_Taxonomy;
use WP_Term;

/**
 * Class TermAuth
 * @package Inpsyde\MultilingualPress\Framework\Auth
 */
final class TermAuth implements Auth
{
    /**
     * @var WP_Term
     */
    private $term;

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @param WP_Term $term
     * @param Nonce $nonce
     */
    public function __construct(WP_Term $term, Nonce $nonce)
    {
        $this->term = $term;
        $this->nonce = $nonce;
    }

    /**
     * @inheritdoc
     */
    public function isAuthorized(): bool
    {
        $taxonomy = get_taxonomy($this->term->taxonomy);

        if (!$taxonomy instanceof WP_Taxonomy || ms_is_switched()) {
            return false;
        }

        return current_user_can($taxonomy->cap->edit_terms) && $this->nonce->isValid();
    }
}
