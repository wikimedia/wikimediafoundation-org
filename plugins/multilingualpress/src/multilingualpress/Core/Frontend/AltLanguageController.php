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

/**
 * Alternate language controller.
 */
class AltLanguageController
{
    const FILTER_HREFLANG_TYPE = 'multilingualpress.hreflang_type';

    /**
     * @var int
     */
    private $type;

    public function __construct()
    {
        /**
         * Filters the output type for the hreflang links.
         *
         * The type is a bitmask with possible (partial) values available as constants on the
         * renderer interface.
         *
         * @param int $hreflang_type
         */
        $this->type = (int)apply_filters(
            self::FILTER_HREFLANG_TYPE,
            AltLanguageRenderer::TYPE_HTML_LINK_TAG
        );
    }

    /**
     * Registers the given renderer according to the given arguments.
     *
     * @param AltLanguageRenderer $renderer
     * @param string $action
     * @param int $priority
     * @param int $acceptedArgs
     * @return bool
     */
    public function registerRenderer(
        AltLanguageRenderer $renderer,
        string $action,
        int $priority = 10,
        int $acceptedArgs = 1
    ): bool {

        if (!($this->type & $renderer->type())) {
            return false;
        }

        add_action(
            $action,
            static function (...$args) use ($renderer) {
                if (!is_paged()) {
                    $renderer->render(...$args);
                }
            },
            $priority,
            $acceptedArgs
        );

        return true;
    }
}
