# Plugin Management

## Composer-managed plugins

Many plugins in this project are managed (installed, upgraded, and so on) through [`composer.json`](../composer.json). If the [project `.gitignore` file](../.gitignore) excludes a specific plugin from the repository, assume that plugin is meant to be installed via Composer, not by hard-committing individual files.

To upgrade an individual plugin via Composer, run `composer update` with the package name for that plugin's source package. For example to upgrade the `wikipedia-preview` plugin, you would look at the name of that package in the `composer.json` "require" array, and run

```
composer update wpackagist-plugin/wikipedia-preview
```

To upgrade _all_ plugins, along with dependency tools like PHPCS, you may run `composer update` with no arguments. However, note that this can lead to significant version changes across the entire project. Where possible, plugins should be upgraded individually, with each plugin update in a single commit. This makes it easier to diagnose issues when testing the site in the develop or preprod environments.

If a plugin still shows as outdated in the UI or via `wp plugin list` after running any of the `composer update` commands above, you may need to manually adjust the version range for that plugin in `composer.json` and try again. This is sometimes necessary for major version upgrades, for example.

## Paid plugins

Other plugins, for example MultilingualPress, cannot be accessed via Composer because they require their plugin files to be downloaded through a subscriber portal. To upgrade a paid plugin,

- Download the latest version of the plugin (or request the latest zip from a Wikimedia team member with the appropriate account access to download it)
- Remove the old version of the plugin entirely,
  `rm -rf plugins/{plugin name}`
- Extract the zip of the new plugin version into that same `plugins/{plugin name}` folder
- Stage all changes with `git add plugins/{plugin name}`
- Run `git diff` and validate you're seeing file updates that you would expect, for example do you see the version number changing as predicted
- Commit the plugin update to introduce the new code to the repository
- Test locally and in Develop

If you find that a key file for the plugin is not ending up on the deployed environment, you may need to use `git add -f plugins/{plugin name}` to force Git to track some file which the top-level `.gitignore` is excluding.
