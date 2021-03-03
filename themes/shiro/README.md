Reaktiv Studios Starter Theme
===

This is the Reaktiv Studios starter theme. There are many like it, but this one is ours...

This theme was origianlly forked from the [Underscores](https://github.com/Automattic/_s) with quite a few additions. The following documentation runs through the basics of the theme and any commands you may need to follow.

Setting things up
---------------
This starter theme is meant to be a starting point for new theme projects. To make initial setup and configuration either, this theme _also_ has a built-in setup process via gulp. This gulp task will rename the underscores prefix(`_s`) in all theme files and download Bootstrap and include files in the main stylesheet.

But all of this is optional, and it starts with a configuration object in `package.json` named `themeConfig.` For reference, that JSON block already exists with some helpful defaults. It looks like this:

```json
  "themeConfig": {
    "rename": "starter",
    "bootstrap": true,
    "bootstrapFiles": [
      "type",
      "images",
      "code",
      "grid",
      "tables",
      "forms",
      "buttons",
      "transitions",
      "dropdown",
      "button-group",
      "pagination",
      "media",
      "list-group",
      "responsive-embed",
      "close",
      "utilities"
    ]
  }
```

Here's a breakdown of those variables:

**`rename`**

 _`string` or `object`_
 
Rename is used to determine string replacements for PHP files in the theme. This will replace all instances of `_s` with the string you chose.

If you pass a simple string to this variable, then both the prefix and text domain's throughout the theme will be replaced with that string. However, you can also pass an object with a separate `prefix`, `text_domain`, and `directory` if those need to be different.
```
"rename": "starter"
```

```
"rename": {
  "prefix": "starter_prefix",
  "text_domain": "starter_domain",
  "directory": "starter"
}
```

---

**`bootstrap`**

_`boolean`_

Whether or not Bootstrap CSS should be added to the theme. If set to true, Bootstrap will be downloaded via npm and all SASS files will be moved into the `base/vendor/bootstrap` folder.
```
"bootstrap": true
```
---

**`bootstrapFiles`**

_`array`_

List of files to include from Bootstrap. All files will be copied over into the vendor folder, but specifying files in this array will add `@import` to the main style.scss file located at `assets/src/sass/style.scss`. Files added to the array should match the name of the file in the Bootstrap scss folder, without the `_` prefix or `.scss` extension.
```
"bootstrapFiles": [ ... ]
```
---

Once the `themeConfig` object has been configured, you can setup the theme by running (making sure to `npm install` first):
```
gulp setup
```

_**Please note, and this is important:** The `gulp setup` task is extremely destructive and self-erasing. That means that when its all done, it will remove all traces of itself and the theme will be configured. There's no way to reverse the process. This should be run at the very beginning of projects._

Folder Structure
---------------
The basic folder structure keeps things fairly simple and organized by type.

The `assets` folder contains front-end files in the `src` sub-directory. Any files that need to be concatenated are output into `dist` and files should be enqueued by the theme from there.

The `inc` folder contains all PHP files that aren't required in the root directory by WordPress. It includes several helpful functions from Underscores and the team. Feel free to remove any functions from here not in use.

The `template-parts` folder contains template parts used by larger templates in the root directory.

Build Process
---------------
This theme uses [Gulp](http://gulpjs.com/) & [Webpack](https://webpack.js.org) for all its build process needs. They will help you to concatenate, lint and build your files. This also includes livereload, which will automatically inject CSS changes, and reload the live page whenever changes are made to JS or PHP files.

In order to make sure your development environment works the way it should, drop this into your `wp-config.php` file.

```
define( 'RKV_ENV', 'local' );
```

The following tasks are available to you:

* `npm run build`
This builds out the assets and runs the following tasks: `styles`, `scripts`

* `npm run lint`
Lints all file types and runs the following tasks: `csslint`, `jslint` `phplint`

* `npm run start`
Begins watching front-end assets (scripts and styles) and compiles them when changed. This will also start the livereload script, which refreshes the page when changes are made.

* `npm run download-style-guide-sass`
Download the variables file in the style-guide. This is only required when the style guide has changed.

Bootstrap Setup
---------------
For Bootstrap setup instructions see the [Setting Things Up](#setting-things-up) section above. The Bootstrap setup outlined below is optional.

The CSS for this starter theme could best be described as Bootstrap-like. That means that baseline CSS is taken from Bootstrap for things like grids, tables, forms, etc. but more specialized components have been removed. Bootstrap code is included in `assets/src/sass/base/vendor/bootstrap`. In addition to a CSS reset, baseline typography and styles, the following Bootstrap components are available for use in markup by default:
* [Grid](https://v4-alpha.getbootstrap.com/layout/grid/)
* [Typography](https://v4-alpha.getbootstrap.com/content/typography/)
* [Media Object](https://v4-alpha.getbootstrap.com/layout/media-object/)
* [Buttons](https://v4-alpha.getbootstrap.com/components/buttons/)
* [Dropdowns](https://v4-alpha.getbootstrap.com/components/dropdowns/)
* [Forms](https://v4-alpha.getbootstrap.com/components/forms/)
* [Tables](https://v4-alpha.getbootstrap.com/content/tables/)
* [List Group](https://v4-alpha.getbootstrap.com/components/list-group/)
* [Pagination](https://v4-alpha.getbootstrap.com/components/pagination/)
* [Utilities](https://v4-alpha.getbootstrap.com/utilities/borders/)

If you'd like to remove some of these, or add different files, simply add the name of the file to the `themeConfig` object or add a `@import` line to `style.scss`.

Bootstrap code _should not_ be edited directly. Instead, please use variables listed in `assets/src/sass/_vars.scss`. Whenever possible, these variables should also be used in custom CSS. If a new variable is needed, add it to this file.

CSS
---------------
Custom CSS should be placed in a folder outside of `base/vendor` (i.e. `assets/src/sass/components/`). CSS should follow the BEM naming convention and files should be clearly commented. As an example of this, you can see a template for new SASS partials at `assets/src/sass/_example.scss`. 

Remember, `gulp styles` compiles your SASS to a `style.css` file in the root directory of the theme and lints your files according to baseline rules.

JS
---------------
JS should follow the [WordPress coding standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/javascript/). The only addition is to use [JSDoc](http://eslint.org/docs/rules/require-jsdoc) when commenting on functions.

`gulp scripts` will concatenate all JavaScript files in `assets/src/js/` to `assets/dist/` and lint them according to WordPress rules.

PHP
---------------
PHP should follow the [WordPress coding standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/). PHP Codesniffer is configured in `ruleset.xml` to verify WordPress and WordPress VIP rules are in use. `gulp phplint` will run PHPCS on your entire project.

Icons
---------------
An SVG icon system has been included as a sprite sheet generated by gulp with `gulp svg`. In order to keep the file size of this sprite down, only icons which are added to the theme's `assets/src/svg/individual` folder are compiled and included in this sprite file. The compiled icon sprite is located at `assets/dist/icons.svg`. Instructions on how to add additional icons to this sprite are included below.

##### Using icons in the theme
The helper function `_s_show_icon` is available to make using icons as simple as possible. The only parameter for this function is the name of the SVG to display. This name matches the name of the original SVG file.

````php
_s_show_icon( 'search' );
````

When the SVG is output, it will include the `icon` and `icon-{icon-name}` CSS classes so that it can be targeted and styled.

Localization
---------------
A `.pot` file can be generated based on the text domain specified in the `themeConfig.rename` variable from `package.json` used anywhere in the parent theme. If this option does not exist, no .pot file will be created. The file will be output to `languages/{text_domain}.pot`.

````
gulp pot
````
