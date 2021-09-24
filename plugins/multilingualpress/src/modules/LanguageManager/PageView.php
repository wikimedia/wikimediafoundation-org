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

namespace Inpsyde\MultilingualPress\Module\LanguageManager;

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Language Manager Page View
 */
final class PageView implements SettingsPageView
{
    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var TableFormView
     */
    private $table;

    /**
     * PageView constructor.
     * @param Nonce $nonce
     * @param Request $request
     */
    public function __construct(Nonce $nonce, Request $request, TableFormView $table)
    {
        $this->nonce = $nonce;
        $this->request = $request;
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        ?>
        <div class="wrap">
            <h1><?= esc_html(get_admin_page_title()) ?></h1>
            <?php settings_errors() ?>
            <?php $this->renderForm() ?>
        </div>
        <?php
    }

    /**
     * Render the form
     *
     * @return void
     */
    private function renderForm()
    {
        ?>
        <form class="mlp-language-manager-form"
              action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
              method="post"
        >
            <?php $this->table->render() ?>
            <?php printNonceField($this->nonce) ?>
            <input type="hidden"
                   name="action"
                   value="<?php echo esc_attr(RequestHandler::ACTION) ?>"
            />
            <?php submit_button(__('Save Languages', 'multilingualpress')) ?>
        </form>
        <?php
    }
}
