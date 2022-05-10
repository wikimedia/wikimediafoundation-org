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

namespace Inpsyde\MultilingualPress\TranslationUi\Post\Field;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class EditLink
{

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        if (!$context->hasRemotePost()) {
            return;
        }
        ?>
        <tr>
            <th scope="row">
                <?php
                print esc_html_x(
                    'Edit Link',
                    'edit post link translation meta box',
                    'multilingualpress'
                );
                ?>
            </th>
            <td>
                <?php
                $help = _x(
                    '(open in current page)',
                    'edit post link translation meta box',
                    'multilingualpress'
                );
                printf(
                    '<a href="%1$s">%1$s</a> <span class="description">%2$s</span>',
                    esc_url(get_edit_post_link($context->remotePost())),
                    esc_html($help)
                );
                ?>
            </td>
        </tr>
        <?php
    }
}
