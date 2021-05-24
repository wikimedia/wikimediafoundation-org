# Wikimedia Foundation website

This repository hosts the files for the Wikimedia Foundation website (wikimediafoundation.org). More information about the website is available on Meta-Wiki: https://meta.wikimedia.org/wiki/Wikimedia_Foundation_website

## Usage

The production repository is privately hosted on GitHub and maintained by Automattic Inc. and the Wikimedia Foundation Communications department.

A public repository is mirrored and made available: https://github.com/wikimedia/wikimediafoundation-org

## Updating mirror

The process for updating the mirror is documented by GitHub: https://help.github.com/en/articles/duplicating-a-repository

Command to run from private repository:
  git push --mirror https://github.com/wikimedia/wikimediafoundation-org.git

## Updating Localization

There are several composer scripts here that make use of `wp i18n` to help manage localization files. Run `composer run -l` to see a list of available scripts.

Once you know which script you'd like to run, the pattern is `composer [script name]` e.g. `composer make-shiro-frontend-pot`.

**⚠️ You must have WP-CLI installed for these scripts to work.**

There are two namespaces to help simplify translation:

- `shiro` - These are "frontend" strings--ones that are likely to be seen by visitors to the site.
- `shiro-admin` - These are "backend" strings--ones that will only be seen by editors and administrators.
