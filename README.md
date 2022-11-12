# Wikimedia Foundation website

This repository hosts the files for the Wikimedia Foundation website (wikimediafoundation.org). More information about the website is available on Meta-Wiki: https://meta.wikimedia.org/wiki/Wikimedia_Foundation_website

## Usage

The production repository is privately hosted on GitHub and maintained by Automattic Inc. and the Wikimedia Foundation Communications department.

A public repository is mirrored and made available: https://github.com/wikimedia/wikimediafoundation-org

## Submodules

This theme uses several other theme and plugins as Git submodules. When cloning the repository, use the `--recursive` flag for `git clone`, or else run

```
git submodule init
git submodule update --recursive
```

after cloning to pull in required submodules.

## Updating mirror

The process for updating the mirror is documented by GitHub: https://help.github.com/en/articles/duplicating-a-repository

Command to run from private repository:
  git push --mirror https://github.com/wikimedia/wikimediafoundation-org.git

## Updating plugins

Some plugins are managed via [`composer.json`](./composer.json), while others (for example paid plugins like MultilingualPress) are committed into the repository manually within the [`plugins/` directory](./plugins/). See the [additional README in that folder](./plugins/) for more information on how to update individual plugins.

### Updating themes

The `shiro` theme is a submodule referencing [`wikimedia/shiro-wordpress-theme`](https://github.com/wikimedia/shiro-wordpress-theme). The theme handles its own production builds, which are distributed on the `release` branch of that repository. To update the theme submodule,

```bash
# first, pull the latest release branch commit
cd themes/shiro
git checkout release
git pull origin release
# then commit the updated submodule
cd ..
git add shiro
```

## Setup

The theme uses node & npm for dependency management and asset build pipeline. The `engines` field in `themes/shiro/package.json` defines the correct versions of node and npm, and will cause `npm install` to fail if those versions are not in use.

If you're [using nvm](https://github.com/nvm-sh/nvm#installing-and-updating), running `nvm use` from the theme directory will automatically set (and install if necessary) the correct version of node, which will *usually* include the correct version of npm.

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
