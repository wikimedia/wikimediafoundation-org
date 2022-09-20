<?php

declare(strict_types=1);

$languages = $context['languages'] ?: [];
$flagDisplayType = $context['flagDisplayType'] ?: '';
?>
<style>
    .wp-block-mlp-language-menu  {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5em;

        flex-direction: row;
        align-items: center;
        justify-content: flex-start;

    }

    .wp-block-mlp-language-menu > * {
        margin: 0;
    }
</style>
<nav class="is-responsive wp-block-navigation wp-block-mlp-language-menu">
    <ul class="wp-block-navigation__container">
        <?php foreach ($languages as $language) :
            $languageName = $language['name'] ?? '';
            $siteFlagUrl = $language['flagUrl'] ?? '';
            $siteFlagImageAlt = sprintf(
                /* translators: %s: The site language name. */
                __('%s language flag', 'multilingualpress'),
                esc_html($languageName)
            );
            ?>
            <li class="wp-block-navigation-item">
                <a class="wp-block-navigation-item__content" href="<?= esc_url($language['url']);?>">
                    <span class="wp-block-navigation-item__label">
                        <?php if ($flagDisplayType === 'only_flag') :?>
                            <img src="<?= esc_url($siteFlagUrl);?>" alt="<?= esc_attr($siteFlagImageAlt);?>" />
                            <span class="screen-reader-text"><?= esc_html($languageName);?></span>
                        <?php elseif ($flagDisplayType === 'flag_and_text') :?>
                            <img src="<?= esc_url($siteFlagUrl);?>" alt="<?= esc_attr($siteFlagImageAlt);?>" />
                            <?= esc_html($languageName);?>
                        <?php else :?>
                            <?= esc_html($languageName);?>
                        <?php endif;?>
                    </span>
                </a>
            </li>
        <?php endforeach;?>
    </ul>
</nav>
