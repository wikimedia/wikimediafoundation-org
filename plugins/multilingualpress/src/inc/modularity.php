<?php

declare(strict_types=1);

namespace Inpsyde\MultilingualPress;

use AppendIterator;
use ArrayIterator;
use Inpsyde\MultilingualPress\Framework\Module\FileLocator;
use Inpsyde\MultilingualPress\Framework\Module\ModuleLocator;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection;
use IteratorIterator;

//phpcs:disable WordPressVIPMinimum.Constants.ConstantString.NotCheckingConstantName
if (!defined(__NAMESPACE__ . '\\ACTION_ADD_SERVICE_PROVIDERS')) {
    return;
}
//phpcs:enable

return static function (string $rootDir) {
    add_action(
        ACTION_ADD_SERVICE_PROVIDERS,
        static function (ServiceProvidersCollection $providers) use ($rootDir) {
            $moduleFileName = 'module.php';
            $maxDepth = 1;
            $dirs = apply_filters('multilingualpress.module_dirs', [
                "$rootDir/src/multilingualpress/",
                "$rootDir/src/modules/",
                "$rootDir/modules",
            ]);

            $moduleFiles = new AppendIterator();
            foreach ($dirs as $moduleDir) {
                if (!file_exists($moduleDir) || !is_dir($moduleDir)) {
                    continue;
                }
                $moduleFiles->append(new IteratorIterator(new FileLocator(
                    $moduleDir,
                    $moduleFileName,
                    $maxDepth
                )));
            }

            $moduleFiles = new ArrayIterator(
                apply_filters(
                    'multilingualpress.module_files',
                    iterator_to_array($moduleFiles)
                )
            );
            $modules = new ModuleLocator($moduleFiles);

            foreach ($modules as $module) {
                assert($module instanceof ServiceProvider);
                $providers->add($module);
            }
        }
    );
};
