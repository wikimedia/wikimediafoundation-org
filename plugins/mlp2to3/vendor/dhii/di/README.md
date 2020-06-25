# Dhii - DI

[![Build Status](https://travis-ci.org/Dhii/di.svg?branch=master)](https://travis-ci.org/Dhii/di)
[![Code Climate](https://codeclimate.com/github/Dhii/di/badges/gpa.svg)](https://codeclimate.com/github/Dhii/di)
[![Test Coverage](https://codeclimate.com/github/Dhii/di/badges/coverage.svg)](https://codeclimate.com/github/Dhii/di/coverage)
[![Join the chat at https://gitter.im/Dhii/di](https://badges.gitter.im/Dhii/di.svg)](https://gitter.im/Dhii/di?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
Simple, granular, standards-compliant DI container implementations.

### Features
- Is an implementation of the [PSR-11][] standard.
- Uses some other [standards][dhii/data-container-interface] published separately.
- Includes support for the [delegate lookup][] feature, a.k.a. composite containers, with intuitive override order.
- Easily extensible and adaptable.

### Classes
- [`CachingContainer`][CachingContainer] - A simple container that resolves callable service definitions and caches the
result, guaranteeing the same instance every time. [Cache sold separately][dhii/memoize-memory].
- [`ContainerAwareCachingContainer`][ContainerAwareCachingContainer] - A container that is aware of another container,
perhaps a parent one. Callable definitions will receive the top-most parent container of the chain when resolving. Use
it with [`CompositeContainer`][CompositeContainer] to implement the 
- [`AbstractBaseContainer`][AbstractBaseContainer] - Common functionality for DI containers that store services as internal data.
- [`AbstractBaseCachingContainer`][AbstractBaseCachingContainer] - Common functionality for DI containers that cache resolved services.


[Dhii]: https://github.com/Dhii/dhii
[dhii/memoize-memory]: https://github.com/Dhii/memoize-memory
[PSR-11]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md

[delegate lookup]:                                      https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup-meta.md
[dhii/data-container-interface]:                        https://packagist.org/packages/dhii/data-container-interface

[AbstractBaseContainer]:                                src/AbstractBaseContainer.php
[AbstractBaseCachingContainer]:                         src/AbstractBaseCachingContainer.php
[CachingContainer]:                                     src/CachingContainer.php
[ContainerAwareCachingContainer]:                       src/ContainerAwareCachingContainer.php

[CompositeContainer]:                                   https://github.com/Dhii/composite-container/blob/develop/src/CompositeContainer.php
