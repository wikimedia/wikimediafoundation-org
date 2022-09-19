# Plugin Management

Many plugins in this project are managed (installed, upgraded, and so on) through [`composer.json`](../composer.json). If the [project `.gitignore` file](../.gitignore) excludes a specific plugin from the repository, assume that plugin is meant to be installed via Composer, not by hard-committing individual files.

To upgrade an individual plugin via Composer, run `composer update` with the package name for that plugin's source package. For example to upgrade the `wikipedia-preview` plugin, you would look at the name of that package in the `composer.json` "require" array, and run

```
composer update wpackagist-plugin/wikipedia-preview
```

To upgrade _all_ plugins, along with dependency tools like PHPCS, you may run `composer update` with no arguments. However, note that this can lead to significant version changes across the entire project. Where possible, plugins should be upgraded individually, with each plugin update in a single commit. This makes it easier to diagnose issues when testing the site in the develop or preprod environments.

If a plugin still shows as outdated in the UI or via `wp plugin list` after running any of the `composer update` commands above, you may need to manually adjust the version range for that plugin in `composer.json` and try again. This is sometimes necessary for major version upgrades, for example.
