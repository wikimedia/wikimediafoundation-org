# Dhii - WP Containers

[![Build Status](https://travis-ci.org/Dhii/wp-containers.svg?branch=develop)](https://travis-ci.org/Dhii/wp-containers)
[![Code Climate](https://codeclimate.com/github/Dhii/wp-containers/badges/gpa.svg)](https://codeclimate.com/github/Dhii/wp-containers)
[![Test Coverage](https://codeclimate.com/github/Dhii/wp-containers/badges/coverage.svg)](https://codeclimate.com/github/Dhii/wp-containers/coverage)
[![Join the chat at https://gitter.im/Dhii/wp-containers](https://badges.gitter.im/Dhii/wp-containers.svg)](https://gitter.im/Dhii/wp-containers?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
[PSR-11][] container implementations that wrap some WP features, for convenience and interoperability.

## Features:
- Retrieve sites by key:

    ```php
    use Dhii\Wp\Containers\Sites;
    use WP_Site;
  
    $sites = new Sites();
    $site2 = $sites->get(2);
    assert($site2 instanceof WP_Site);
    ```

- Retrieve site options by key:

    ```php
    use Dhii\Wp\Containers\Options\BlogOptions;
    use Dhii\Wp\Containers\Options\BlogOptionsContainer;
    
    // Set up sites container (see other example)
    // ...

    // Definition
    $optionsContainer = new BlogOptionsContainer(
        function ($id) {
            return new BlogOptions($id, uniqid('default-option-value'));
        },
        $sites
    );
    
    // Usage
    $blog3Options = $optionsContainer->get(3);
    $myOption = $blog3Options->get('my_option');
    ```
    
- Retrieve site meta by key:

    ```php
    use Dhii\Wp\Containers\Options\SiteMeta;
    use Dhii\Wp\Containers\Options\SiteMetaContainer;
    
    // Set up sites container (see other example)
    // ...

    // Definition
    $metaContainer = new SiteMetaContainer(
        function ($id) {
            return new SiteMeta($id, uniqid('default-meta-value'));
        },
        $sites
    );
    
    // Usage
    $blog4Meta = $metaContainer->get(4);
    $myMeta = $blog4Meta->get('my_meta');
    ```

[Dhii]: https://github.com/Dhii/dhii
[PSR-11]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md
