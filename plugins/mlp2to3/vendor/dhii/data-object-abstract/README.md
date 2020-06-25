# Dhii - Data - Object - Abstract

[![Build Status](https://travis-ci.org/Dhii/data-object-abstract.svg?branch=develop)](https://travis-ci.org/Dhii/data-object-abstract)
[![Code Climate](https://codeclimate.com/github/Dhii/data-object-abstract/badges/gpa.svg)](https://codeclimate.com/github/Dhii/data-object-abstract)
[![Test Coverage](https://codeclimate.com/github/Dhii/data-object-abstract/badges/coverage.svg)](https://codeclimate.com/github/Dhii/data-object-abstract/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/data-object-abstract/version)](https://packagist.org/packages/dhii/data-object-abstract)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
This package contains abstract functionality for data objects. Data objects are objects that can contain, and optionally
manipulate, some internal data. The implementations in this package are based on containers, which means that anything
returned by [`_normalizeContainer`][NormalizeContainerCapableTrait#_normalizeContainer()] can have a data object based
on it. Another advantage of this implementation is that a data key can be anything that passes normalization by
[`normalizeString()`][NormalizeStringCapableTrait#_normalizeString()]. All this makes this package an excellent base
for flexible, extensible data objects.

**Important Notice**: While the traits that access the internal store can work with any container, the `_getDataStore()`
method used by them MUST return an object for the methods that write to the store to have an effect. This is to avoid
having to use references, which would complicate the code and make it more error-prone, and less optimized.

### Traits
- [`GetDataCapableTrait`][GetDataCapableTrait] - Retrieves data by key from the internal store.
- [`SetDataCapableTrait`][SetDataCapableTrait] - Sets a data value by key in the internal store.
- [`SetManyCapableTrait`][SetManyCapableTrait] - Sets multiple data values by key-value map in the internal store.
- [`HasDataCapableTrait`][HasDataCapableTrait] - Establishes whether a key exists in the internal store.
- [`UnsetDataCapableTrait`][UnsetDataCapableTrait] - Removes data by key from the internal store.
- [`UnsetManyCapableTrait`][UnsetManyCapableTrait] - Removes multiple values by key list from the internal store.
- [`DataStoreAwareContainerTrait`][DataStoreAwareContainerTrait] - Retrieves the internal store.
- [`CreateDataStoreCapableTrait`][CreateDataStoreCapableTrait] - Creates an object that can serve as an internal store.
- [`NormalizeKeyCapableTrait`][NormalizeKeyCapableTrait] - Makes sure that a data key can be used to retrieve a value from the internal store.


[Dhii]: https://github.com/Dhii/dhii

[GetDataCapableTrait]:                                                  src/GetDataCapableTrait.php
[SetDataCapableTrait]:                                                  src/SetDataCapableTrait.php
[SetManyCapableTrait]:                                                  src/SetManyCapableTrait.php
[HasDataCapableTrait]:                                                  src/HasDataCapableTrait.php
[UnsetDataCapableTrait]:                                                src/UnsetDataCapableTrait.php
[UnsetManyCapableTrait]:                                                src/UnsetManyCapableTrait.php
[DataStoreAwareContainerTrait]:                                         src/DataStoreAwareContainerTrait.php
[CreateDataStoreCapableTrait]:                                          src/CreateDataStoreCapableTrait.php
[NormalizeKeyCapableTrait]:                                             src/NormalizeKeyCapableTrait.php

[NormalizeContainerCapableTrait#_normalizeContainer()]:                 https://github.com/Dhii/container-helper-base/blob/develop/src/NormalizeContainerCapableTrait.php#L32
[NormalizeStringCapableTrait#_normalizeString()]:                       https://github.com/Dhii/normalization-helper-base/blob/develop/src/NormalizeStringCapableTrait.php#L30
