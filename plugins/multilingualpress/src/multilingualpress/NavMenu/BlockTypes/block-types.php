<?php

use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\NavMenu\BlockTypes\LanguageMenuContextFactory;
use Inpsyde\MultilingualPress\SiteFlags\ServiceProvider as SiteFlags;

return static function (array $prev, Container $container): array {
    $relatedSitesConfig = $container->get('multilingualpress.NavMenu.RelatedSites');
    $moduleManager = $container->get(ModuleManager::class);
    $contextFactory = $container->get(LanguageMenuContextFactory::class);
    $templatePath = $container->get('multilingualpress.NavMenu.BlockTypesTemplatePath');
    return [
        [
            'name' => 'multilingualpress/language-menu',
            'category' => 'widgets',
            'icon' => 'admin-site',
            'title' => __('MultilingualPress: Language Menu', 'multilingualpress'),
            'description' => __('Choose a language from the settings sidebar.', 'multilingualpress'),
            'attributes' => [
                'languages' => [
                    'type' => 'array',
                ],
                'titles' => [
                    'type' => 'array',
                ],
                'flagDisplayType' => [
                    'type' => 'string',
                ],
            ],
            'extra' => [
                'relatedSites' => $relatedSitesConfig,
                'isSiteFlagsModuleActive' => $moduleManager->isModuleActive(SiteFlags::MODULE_ID),
            ],
            'templatePath' => "{$templatePath}language-menu.php",
            'contextFactory' => $contextFactory,
        ],
    ];
};
