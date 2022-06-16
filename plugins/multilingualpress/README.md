# MultilingualPress

> The multisite-based plugin for your multilingual WordPress websites.

![MultilingualPress](resources/assets/banner-1544x500.png)

## Description

Run each language in a separate site, and connect the content in a lightweight user interface. Use a customizable widget
to link to all sites.

This plugin lets you connect an unlimited amount of sites with each other.
Set a main language for each site, create relationships (connections), and start writing. You get a new field now to
create a linked post on all the connected sites automatically.
They are accessible via the post/page editor screen - you can switch back and forth to translate them.

In contrast to most other translation plugins there is **no lock-in effect**: When you disable our plugin, all sites
will still work as separate sites without any data-loss or garbage output.

## Features

- Set up unlimited site relationships in the site manager.
- Translate posts, pages and taxonomy terms like categories or tags.
- Add translation links to any nav menu.
- No lock-in: After deactivation, all sites will still work.
- SEO-friendly URLs and permalinks.
- Support for top-level domains per language (via multisite domain mapping).
- Automatic `hreflang` support.
- Support for custom post types.
- Automatically redirect to the user's preferred language version of a post.
- Duplicate sites. Use one site as template for new site, copy *everything:* Posts, attachments, settings for plugins
and themes, navigation menus, categories, tags and custom taxonomies.
- Synchronized trash: move all connected post to trash with one click.
- Change relationships between translations or connect existing posts.
- Show posts with incomplete translations in a dashboard widget.

## Installation

### Requirements

* WordPress multisite 5.0 or higher.
* PHP 7.2 or higher.

If you're new to WordPress multisite, you might find our [WordPress multisite installation
tutorial](http://make.multilingualpress.org/2014/02/how-to-install-multi-site/) helpful.

### Installation

Use the installer via back-end of your install or ...

1. Unpack the download-package.
2. Upload the files to the `/wp-content/plugins/` directory.
3. Activate the plugin through the **Network/Plugins** menu in WordPress and click **Network Activate**.
4. Go to **All Sites**, **Edit** each site, then select the tab **MultilingualPress** to configure the settings. You
need at least two sites with an assigned language.

## Frequently Asked Questions

### Will MultilingualPress translate my content?

No, it will not. It manages relationships between sites and translations, but it doesn't change the content.

### Can I use MultilingualPress on a single-site installation?

That would require changes to the way WordPress stores post content. Other plugins do that; we think this is wrong,
because it creates a lock-in: you would lose access to your content after the plugin deactivation.

### I'm new to WordPress multisite. Are there any tutorials to get me started?

Yes, just have a look at our [WordPress multisite installation
tutorial](http://make.multilingualpress.org/2014/02/how-to-install-multi-site/).

## License

Copyright (c) 2018 Inpsyde GmbH

This code is licensed under the [GPLv2+ License](LICENSE).
