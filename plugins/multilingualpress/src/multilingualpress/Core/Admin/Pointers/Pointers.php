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

namespace Inpsyde\MultilingualPress\Core\Admin\Pointers;

use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Http\Request;

/**
 * WordPress Internal Pointers manager.
 */
class Pointers
{
    const USER_META_KEY = '_dismissed_mlp_pointers';
    const ACTION_AFTER_POINTERS_CREATED = 'multilingualpress.after_pointers_created';

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Request $request
     * @param Repository $repository
     * @param AssetManager $assetManager
     */
    public function __construct(
        Request $request,
        Repository $repository,
        AssetManager $assetManager
    ) {

        $this->request = $request;
        $this->repository = $repository;
        $this->assetManager = $assetManager;
    }

    /**
     * @return void
     * @throws AssetException
     */
    public function createPointers()
    {
        if (!current_user_can('create_sites')) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen->id) {
            return;
        }

        list($pointers, $ajaxAction) = $this->repository->forScreen($screen->id);
        if (!$pointers) {
            return;
        }

        $dismissedPointers = explode(
            ',',
            (string)get_user_meta(get_current_user_id(), self::USER_META_KEY, true)
        );
        if ($this->currentPointersDismissed(array_keys($pointers), $dismissedPointers)) {
            return;
        }

        $this->enqueuePointers($pointers, $ajaxAction);

        do_action(self::ACTION_AFTER_POINTERS_CREATED, $screen);
    }

    /**
     * @param array $pointers
     * @param string $ajaxAction
     * @return void
     * @throws AssetException
     */
    public function enqueuePointers(array $pointers, string $ajaxAction)
    {
        wp_enqueue_style('wp-pointer');
        wp_enqueue_script('wp-pointer');

        $this->assetManager->enqueueScriptWithData(
            'pointers',
            'multilingualPressPointersData',
            [
                'pointers' => $pointers,
                'dismissButtonText' => _x('Dismiss guide', 'pointers', 'multilingualpress'),
                'okButtonText' => _x('OK', 'pointers', 'multilingualpress'),
                'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
                'ajaxAction' => $ajaxAction,
            ]
        );
    }

    /**
     * @return void
     */
    public function dismiss()
    {
        $pointer = $this->request->bodyValue('pointer', INPUT_POST, FILTER_SANITIZE_STRING);
        if (!$pointer) {
            return;
        }

        $dismissedPointers = explode(
            ',',
            (string)get_user_meta(get_current_user_id(), self::USER_META_KEY, true)
        );

        if (in_array($pointer, $dismissedPointers, true)) {
            return;
        }

        $dismissedPointers[] = $pointer;
        $dismissed = implode(',', $dismissedPointers);

        update_user_meta(get_current_user_id(), self::USER_META_KEY, $dismissed);
    }

    /**
     * @param array $pointers
     * @param array $dismissedPointers
     * @return bool
     */
    private function currentPointersDismissed(array $pointers, array $dismissedPointers): bool
    {
        foreach ($pointers as $pointer) {
            if (in_array($pointer, $dismissedPointers, true)) {
                return true;
            }
        }

        return false;
    }
}
