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

namespace Inpsyde\MultilingualPress\NavMenu;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

/**
 * Handler for nav menu AJAX requests.
 */
class AjaxHandler
{
    const ACTION = 'multilingualpress_add_languages_to_nav_menu';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var ItemRepository
     */
    private $repository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Nonce $nonce
     * @param ItemRepository $repository
     * @param Request $request
     */
    public function __construct(
        Nonce $nonce,
        ItemRepository $repository,
        Request $request
    ) {

        $this->nonce = $nonce;
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * Handles the AJAX request and sends an appropriate response.
     */
    public function handle()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_' . self::ACTION)) {
            wp_send_json_error();
        }

        $sites = $this->siteIdsFromRequest();

        if (!$sites) {
            wp_send_json_error();
        }

        $menuId = (int)$this->request->bodyValue(
            'menu',
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT
        );

        if (!$menuId) {
            wp_send_json_error();
        }

        $items = $this->repository->itemsForSites($menuId, ...$sites);

        /**
         * Contains the Walker_Nav_Menu_Edit class.
         */
        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

        wp_send_json_success(
            walk_nav_menu_tree(
                $items,
                0,
                (object)[
                    'after' => '',
                    'before' => '',
                    'link_after' => '',
                    'link_before' => '',
                    'walker' => new \Walker_Nav_Menu_Edit(),
                ]
            )
        );
    }

    /**
     * @return array
     */
    private function siteIdsFromRequest(): array
    {
        if (!current_user_can('edit_theme_options') || !$this->nonce->isValid()) {
            return [];
        }

        $sites = $this->request->bodyValue(
            'mlp_sites',
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT,
            FILTER_FORCE_ARRAY
        );

        return $sites ? array_filter(wp_parse_id_list($sites)) : [];
    }
}
