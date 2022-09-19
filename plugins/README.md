# Plugin Management

Many plugins in this project are managed (installed, upgraded, and so on) through [`composer.json`](../composer.json). If there is a `.gitignore` entry in this directory for a plugin, assume it is meant to be installed via Composer, not by hard-committing individual files.

To upgrade an individual plugin via Composer, run `composer update` with the package name for that plugin's source package. For example to upgrade the `wikimedia-preview` plugin, you would look at the name of that package in the `composer.json` "require" array, and run

```
composer update wikimedia/wikipediapreview-wordpress
```
