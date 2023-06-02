# Wikimedia Foundation website

This repository hosts the files for the Wikimedia Foundation website (wikimediafoundation.org). More information about the website is available on Meta-Wiki: https://meta.wikimedia.org/wiki/Wikimedia_Foundation_website

## Usage

The production repository is privately hosted on GitHub and maintained by Automattic Inc. and the Wikimedia Foundation Communications department.

A public repository is mirrored and made available: https://github.com/wikimedia/wikimediafoundation-org

## Additional documentation

- [Meta-Wiki page on Wikimedia Foundation website](https://meta.wikimedia.org/wiki/Wikimedia_Foundation_website)
- [Meta-Wiki page on Shiro theme](https://meta.wikimedia.org/wiki/Wikimedia_Foundation_website/WordPress_theme)
- [Private development documentation](https://github.com/humanmade/Wikimedia/wiki)
- [Public usage and design documentation on Shiro theme](https://github.com/wikimedia/shiro-wordpress-theme/wiki)

## Local development

This site is intended to be developed using [WordPress VIP's Docker-based local development environment](https://docs.wpvip.com/technical-references/vip-local-development-environment/) via the `vip dev-env` CLI command.

[Install the VIP CLI using these instructions](https://docs.wpvip.com/technical-references/vip-cli/), and then [refer to this guide in the repository wiki for local setup instructions](https://github.com/wpcomvip/wikimediafoundation-org/wiki/Local-development-setup).

## Updating mirror

The process for updating the mirror is documented by GitHub: https://help.github.com/en/articles/duplicating-a-repository

Command to run from private repository:
  git push --mirror https://github.com/wikimedia/wikimediafoundation-org.git

## Developing themes

This theme uses several other themes and plugins as composer dependencies. When cloning the repository, run `composer install` to pull down all site dependencies for use in your local development environment. This will pull down production builds of first-party themes and plugins.

To actively develop any of these plugins in your local development environment, you will need to manually replace the production copy checked out via Composer with a full local git repository for that project. For the Shiro theme, for example, follow these steps to set up the theme for local development after cloning:

```bash
# Install all dependencies
composer install

# Remove the production copy of the theme
rm -rf themes/shiro
# Reinstall from source
composer update wikimedia/shiro-wordpress-theme --prefer-source
# Switch to the `main` branch
cd themes/shiro
git checkout main
# Install and build the theme
nvm use
npm install
npm run build
```

A note on build dependencies: first-party themes and plugins use node & npm for dependency management and asset build pipeline. The `engines` field in `themes/shiro/package.json` defines the correct versions of node and npm, and will cause `npm install` to fail if those versions are not in use.

If you're [using nvm](https://github.com/nvm-sh/nvm#installing-and-updating), running `nvm use` from the theme directory will automatically set (and install if necessary) the correct version of node, which will *usually* include the correct version of npm. The steps above assume `nvm` is available.

## Updating plugins

Some plugins are managed via [`composer.json`](./composer.json), while others (for example paid plugins like MultilingualPress) are committed into the repository manually within the [`plugins/` directory](./plugins/). See the [additional README in that folder](./plugins/) for more information on how to update individual plugins.

### Updating themes

The `shiro` theme is a composer dependency referencing the [`wikimedia/shiro-wordpress-theme`](https://github.com/wikimedia/shiro-wordpress-theme) repository. The theme handles its own production builds, which are distributed on the `release` branch of that repository. To update the theme, adjust the commit hash in `composer.json` to the latest commit on the `release` branch,

```diff
diff --git a/composer.json b/composer.json
index bc224963..6a3dd18f 100644
--- a/composer.json
+++ b/composer.json
@@ -46,7 +46,7 @@
     "humanmade/hm-gutenberg-tools": "^1.6.2",
-    "wikimedia/shiro-wordpress-theme": "dev-release#b36c534839e7ebd63f410b007adc97fd779c38d4",
+    "wikimedia/shiro-wordpress-theme": "dev-release#e5602ffde677511a3f9869f44a91a42f1095b23d",
     "wpackagist-plugin/co-authors-plus": "^3.5",
```

then run `composer update wikimedia/shiro-wordpress-theme` to update the lockfile.

If doing this overwrites a local checkout of the theme repo, follow the steps in [Developing themes](#developing-themes), above, to reset your local environment t

## Updating Localization

There are several composer scripts here that make use of `wp i18n` to help manage localization files. Run `composer run -l` to see a list of available scripts.

Once you know which script you'd like to run, the pattern is `composer [script name]` e.g. `composer make-shiro-frontend-pot`.

**⚠️ You must have WP-CLI installed for these scripts to work.**

There are two namespaces to help simplify translation:

- `shiro` - These are "frontend" strings--ones that are likely to be seen by visitors to the site.
- `shiro-admin` - These are "backend" strings--ones that will only be seen by editors and administrators.

## Visual Testing

This projects includes some scripts and basic configuration to use [BackstopJS](https://github.com/garris/BackstopJS) to test for visual changes.

- `npm run reference` -- Generates reference files based on the current state. If you haven't run any of these scripts yet, you'll need to run this first.
- `npm run test` -- Execute a test based on the scenarios in `backstop.config.js`. You'll need to have reference files first (i.e. by running `npm run reference`).
- `npm run approve` -- Approves "failures" in the previous test, replacing previous reference files.
- `npm run report` -- Opens the latest Backstop report in your browser.

For more information on how to use or modify Backstop, read the documentation: https://github.com/garris/BackstopJS/blob/master/README.md

