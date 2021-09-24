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

namespace Inpsyde\MultilingualPress\Module\QuickLinks;

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection;
use Inpsyde\MultilingualPress\Module\QuickLinks\Model\CollectionFactory;
use Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository;
use InvalidArgumentException;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Class QuickLink
 *
 * @package Inpsyde\MultilingualPress\Module\QuickLinks
 */
class QuickLink
{
    const FILTER_NOFOLLOW_ATTRIBUTE = 'multilingualpress.quicklinks_nofollow';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Repository
     */
    private $settingRepository;

    /**
     * QuickLink constructor.
     * @param CollectionFactory $collectionFactory
     * @param Nonce $nonce
     * @param Repository $settingRepository
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Nonce $nonce,
        Repository $settingRepository
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->nonce = $nonce;
        $this->settingRepository = $settingRepository;
    }

    /**
     * Filter the Post Content
     *
     * Include the Quick Links in the content output
     *
     * @param string $theContent
     * @return string
     */
    public function filter(string $theContent): string
    {
        $post = get_post();

        if (!$post || !is_singular() || !is_main_query()) {
            return $theContent;
        }

        $position = $this->settingRepository->position();
        $currentBlogId = get_current_blog_id();

        try {
            $modelCollection = $this->collectionFactory->create($currentBlogId, $post->ID);
        } catch (InvalidArgumentException $exc) {
            return $theContent;
        }

        if (0 === count($modelCollection)) {
            return $theContent;
        }

        ob_start();
        $this->render($position, $modelCollection);
        $render = ob_get_clean();

        $newContent = (strncmp($position, 'bottom', 6) === 0)
            ? $theContent . $render
            : $render . $theContent;

        return $newContent;
    }

    /**
     * Render
     *
     * @param string $position
     * @param Collection $modelCollection
     */
    protected function render(string $position, Collection $modelCollection)
    {
        ?>
        <div class="mlp-quicklinks mlp-quicklinks--<?= sanitize_html_class($position) ?>">
            <?php
            (4 < count($modelCollection))
                ? $this->renderAsSelect($modelCollection)
                : $this->renderAsLinkList($modelCollection);
            ?>
        </div>
        <?php
    }

    /**
     * Render the Quick Links as a List of Links
     *
     * @param Collection $modelCollection
     */
    protected function renderAsLinkList(Collection $modelCollection)
    {
        /**
         * Filter No Follow Attribute
         *
         * Allow to set the `nofollow` attribute or remove it. Default not applied
         *
         * @params bool $noFollow Default to false
         */
        $noFollow = apply_filters(self::FILTER_NOFOLLOW_ATTRIBUTE, false);

        $rel = 'alternate';
        $noFollow and $rel = "{$rel} nofollow";
        ?>
        <nav class="mlp-quicklinks-list mlp-quicklinks-list--links">
            <h5><?= esc_html_x('Read In:', 'QuickLinks', 'multilingualpress') ?></h5>
            <ul>
                <?php foreach ($modelCollection as $model) : ?>
                    <li class="mlp-quicklinks-list__item">
                        <a class="mlp-quicklinks-link"
                           href="<?= esc_url($model->url()) ?>"
                           lang="<?= esc_attr($model->language()) ?>"
                           hreflang="<?= esc_attr($model->language()) ?>"
                           rel="<?= esc_attr($rel) ?>"
                        >
                            <?= esc_html($model->label()) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }

    /**
     * Render the Quick Links as a Select/Dropdown Element
     *
     * @param Collection $modelCollection
     */
    protected function renderAsSelect(Collection $modelCollection)
    {
        ?>
        <form method="post" action="" class="mlp-quicklinks-form">
            <label class="mlp-quicklinks-form__label" for="mlp_quicklinks_redirect_selection">
                <?= esc_html_x('Read In:', 'QuickLinks', 'multilingualpress') ?>
            </label>

            <select id="mlp_quicklinks_redirect_selection"
                    class="mlp-quicklinks-form__select"
                    name="mlp_quicklinks_redirect_selection"
            >
                <option value=""></option>
                <?php foreach ($modelCollection as $model) : ?>
                    <option value="<?= esc_attr($model->url()) ?>">
                        <?= esc_html($model->label()) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit"
                   class="mlp-quicklinks-form__submit"
                   value="<?= esc_attr_x('Redirect', 'QuickLinks', 'multilingualpress') ?>"
            />

            <?php printNonceField($this->nonce) ?>
        </form>
        <?php
    }
}
