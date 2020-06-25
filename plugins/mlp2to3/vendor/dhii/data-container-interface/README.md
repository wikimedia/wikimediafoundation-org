# Dhii - Data - Container - Interface

[![Build Status](https://travis-ci.org/Dhii/data-container-interface.svg?branch=master)](https://travis-ci.org/Dhii/data-container-interface)
[![Code Climate](https://codeclimate.com/github/Dhii/data-container-interface/badges/gpa.svg)](https://codeclimate.com/github/Dhii/data-container-interface)
[![Test Coverage](https://codeclimate.com/github/Dhii/data-container-interface/badges/coverage.svg)](https://codeclimate.com/github/Dhii/data-container-interface/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/data-container-interface/version)](https://packagist.org/packages/dhii/data-container-interface)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Interfaces for working with data containers.

## Details
This package introduces a couple of completely new
interfaces for granularity, and extends the exceptions interfaces to make them
more useful, while still sticking to the spirit of PSR-11.

Interfaces in this package extend those from [`psr/container`], the [PSR-11]
repository, where applicable. As such, the container itself, as well as the
exceptions, are compatible with PSR-11, in the sense that it's possible to pass
instances of the respective interfaces from this package where PSR-11 interfaces
are expected.

At the same time, the interfaces of this package aim to be compatible with those of
[PSR-16]. This means that theoretically, implementations of these interfaces should
be usable as a cache storage - albeit, in the current state of PSR-16, with some
adaptation. In theory, this should allow all data objects to be accessible in
the same way, regardless of what they are used for.

This package also supports [`dhii/stringable-interface`]: anything that expects
or returns a string key can also accept or return a [`StringableInterface`]
respectively, in addition to a `string`. However, this is optional, and there
is no dependency on that package; implementations are responsible for requiring
`dhii/stringable-interface` themselves.

:book: Please see [Wiki] for detailed explanation.

### Interfaces

- [`HasCapableInterface`] - Allows checking for existence of data value by key.
- [`ContainerInterface`] - Allows checking for and retrieval of data value by key.
- [`ContainerAwareInterface`] - Allows retrieval of a container instance.
- [`SetCapableInterface`] - Allows setting the value for a key.
- [`SetCapableContainerInterface`] - A container that can have a value set for a key.
- [`DeleteCapableInterface`] - Allows deleting a value by key.
- [`DeleteCapableContainerInterface`] - A container that allows deleting a value by key.
- [`ClearCapableInterface`] - Allows deleting all values.
- [`ClearCapableContainerInterface`] - A container that allows deleting all values.
- [`ContainerFactoryInterface`] - A factory that can create containers.
- [`ContainerExceptionInterface`] - An exception that occurs in relation to a container,
and is aware of that container.
- [`NotFoundExceptionInterface`] - An exception that occurs when attempting to
retrieve data for key that is not set, and is also container aware by extension.

## Installation
`composer require dhii/data-container-interface:^0.2`



[Dhii]:                               https://github.com/Dhii/dhii
[Wiki]:                               https://github.com/Dhii/data-container-interface/wiki
[PSR-11]:                             https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md
[PSR-16]:                             https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md

[`psr/container`]:                    https://github.com/php-fig/container
[`dhii/stringable-interface`]:        https://github.com/Dhii/stringable-interface

[`HasCapableInterface`]:              ./src/HasCapableInterface.php
[`ContainerInterface`]:               ./src/ContainerInterface.php
[`ContainerAwareInterface`]:          ./src/ContainerAwareInterface.php
[`SetCapableInterface`]:              ./src/SetCapableInterface.php
[`SetCapableContainerInterface`]:     ./src/SetCapableContainerInterface.php
[`DeleteCapableInterface`]:           ./src/DeleteCapableInterface.php
[`DeleteCapableContainerInterface`]:  ./src/DeleteCapableContainerInterface.php
[`ClearCapableInterface`]:            ./src/ClearCapableInterface.php
[`ClearCapableContainerInterface`]:   ./src/ClearCapableContainerInterface.php
[`ContainerFactoryInterface`]:        ./src/ContainerFactoryInterface.php
[`ContainerExceptionInterface`]:      ./src/Exception/ContainerExceptionInterface.php
[`NotFoundExceptionInterface`]:       ./src/Exception/NotFoundExceptionInterface.php

[`StringableInterface`]:              https://github.com/Dhii/stringable-interface/blob/master/src/StringableInterface.php
