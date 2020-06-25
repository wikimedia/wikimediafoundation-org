# Dhii - Container Helper Base

[![Build Status](https://travis-ci.org/Dhii/container-helper-base.svg?branch=develop)](https://travis-ci.org/Dhii/container-helper-base)
[![Code Climate](https://codeclimate.com/github/Dhii/container-helper-base/badges/gpa.svg)](https://codeclimate.com/github/Dhii/container-helper-base)
[![Test Coverage](https://codeclimate.com/github/Dhii/container-helper-base/badges/coverage.svg)](https://codeclimate.com/github/Dhii/container-helper-base/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/container-helper-base/version)](https://packagist.org/packages/dhii/container-helper-base)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Helper functionality for working with container.

## Traits
- [`ContainerGetCapableTrait`][ContainerGetCapableTrait] - Allows retrieving values by key from anything that is
a known container representation. Types include [`ContainerInterface`][ContainerInterface], `array`,
[`ArrayAccess`][ArrayAccess], and [`stdClass`][stdClass]. Accessing a non-existing key throws a
[`NotFoundExceptionInterface`][NotFoundExceptionInterface].
- [`ContainerGetPathCapableTrait`][ContainerGetPathCapableTrait] - Allows getting data from nested container using
passed path (list of segments).
- [`ContainerHasCapableTrait`][ContainerHasCapableTrait] - Allows checking for values by key from anything that is
a known container implementation.
- [`ContainerListHasCapableTrait`][ContainerListHasCapableTrait] - Allows checking for a key on a list of containers.
- [`ContainerListGetCapableTrait`][ContainerListGetCapableTrait] - Allows retrieving a value from a list of containers.
- [`ContainerSetCapableTrait`][ContainerSetCapableTrait] - Allows setting a value on a writable container.
- [`ContainerSetPathCapableTrait`][ContainerSetPathCapableTrait] - Allows setting a value on a nested container using path.
- [`ContainerSetManyCapableTrait`][ContainerSetManyCapableTrait] - Allows setting multiple values on a writable container.
- [`ContainerUnsetCapableTrait`][ContainerUnsetCapableTrait] - Allows unsetting a value on a writable container.
- [`ContainerUnsetManyCapableTrait`][ContainerUnsetManyCapableTrait] - Allows unsetting multiple values on a writable container.
- [`NormalizeContainerCapableTrait`][NormalizeContainerCapableTrait] - Functionality for container normalization.
- [`NormalizeWritableContainerCapableTrait`][NormalizeWritableContainerCapableTrait] - Functionality for normalizing
writable containers.
- [`NormalizeKeyCapableTrait`][NormalizeKeyCapableTrait] - Allows normalizing container keys.

[Dhii]: https://github.com/Dhii/dhii

[ContainerGetCapableTrait]:                 src/ContainerGetCapableTrait.php
[ContainerGetPathCapableTrait]:             src/ContainerGetPathCapableTrait.php
[ContainerHasCapableTrait]:                 src/ContainerHasCapableTrait.php
[NormalizeContainerCapableTrait]:           src/NormalizeContainerCapableTrait.php
[NormalizeWritableContainerCapableTrait]:   src/NormalizeWritableContainerCapableTrait.php
[ContainerSetCapableTrait]:                 src/ContainerSetCapableTrait.php
[ContainerSetPathCapableTrait]:             src/ContainerSetPathCapableTrait.php
[ContainerSetManyCapableTrait]:             src/ContainerSetManyCapableTrait.php
[ContainerUnsetCapableTrait]:               src/ContainerUnsetCapableTrait.php
[ContainerUnsetManyCapableTrait]:           src/ContainerUnsetManyCapableTrait.php
[NormalizeKeyCapableTrait]:                 src/NormalizeKeyCapableTrait.php
[ContainerListHasCapableTrait]:             src/ContainerListHasCapableTrait.php
[ContainerListGetCapableTrait]:             src/ContainerListGetCapableTrait.php

[ContainerInterface]:                       https://github.com/php-fig/container/blob/master/src/ContainerInterface.php
[NotFoundExceptionInterface]:               https://github.com/php-fig/container/blob/master/src/NotFoundExceptionInterface.php
[ArrayAccess]:                              http://php.net/manual/en/class.arrayaccess.php
[stdClass]:                                 http://php.net/manual/en/language.types.object.php#language.types.object.casting
