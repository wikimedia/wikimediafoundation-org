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

namespace Inpsyde\MultilingualPress\Framework\Setting;

/**
 * Settings box view to show additional information (e.g., for a module).
 */
class SettingsBoxView
{
    const KSES_TAGS = [
        'label' => [
            'class' => true,
            'for' => true,
        ],
    ];

    /**
     * @var SettingsBoxViewModel
     */
    private $model;

    /**
     * @param SettingsBoxViewModel $model
     */
    public function __construct(SettingsBoxViewModel $model)
    {
        $this->model = $model;
    }

    /**
     * Renders the complete settings box content.
     */
    public function render()
    {
        ?>
        <div
            class="mlp-extra-settings-box"
            id="<?= esc_attr($this->model->id()) ?>">
            <?php
            $this->renderTitle();
            $this->renderDescription();
            $this->model->render();
            ?>
        </div>
        <?php
    }

    /**
     * Renders the title, if not empty.
     */
    private function renderTitle()
    {
        $title = $this->model->title();
        if (!$title) {
            return;
        }
        ?>
        <h4><?= esc_html($title) ?></h4>
        <?php
    }

    /**
     * Renders the description, if not empty.
     */
    private function renderDescription()
    {
        $description = $this->model->description();
        if (!$description) {
            return;
        }

        $labelId = $this->model->labelId();
        if ($labelId) {
            $description = sprintf(
                '<label for="%2$s" class="mlp-block-label">%1$s</label>',
                $description,
                esc_attr($labelId)
            );
        }

        echo wp_kses(wpautop($description), self::KSES_TAGS);
    }
}
