# Dhii - Map

[![Build Status](https://travis-ci.org/Dhii/map.svg?branch=develop)](https://travis-ci.org/Dhii/map)
[![Code Climate](https://codeclimate.com/github/Dhii/map/badges/gpa.svg)](https://codeclimate.com/github/Dhii/map)
[![Test Coverage](https://codeclimate.com/github/Dhii/map/badges/coverage.svg)](https://codeclimate.com/github/Dhii/map/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/map/version)](https://packagist.org/packages/dhii/map)
[![Latest Unstable Version](https://poser.pugx.org/dhii/map/v/unstable)](https://packagist.org/packages/dhii/map)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
An iterable container implementation. Is at the same time a [PSR-11] container, and a
[Dhii iterator][dhii/iterator-interface].

### Classes
- [`CountableMap`] - An iterable container, the elements of which can be counted.
- [`AbstractBaseMap`] - Common functionality for maps.
- [`AbstractBaseCountableMap`] - Common functionality for countable maps.
- [`AbstractRecursiveMapFactory`] - Common functionality for factories that create map hierarchies from hierarchies of
iterables.
- [`CountableMapFactory`] - A recursive factory that can create a hierarchy of `CountableMap` objects.
- [`MakeCapableMapTrait`] - Functionality for standardized, normalized creation of maps.
- [`RecursiveFactoryTrait`] - Functionality for standardized map factories that normalize iterable children into maps.


[Dhii]:                                             https://github.com/Dhii/dhii
[PSR-11]:                                           https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md
[dhii/iterator-interface]:                          https://packagist.org/packages/dhii/iterator-interface

[`CountableMap`]:                                   src/CountableMap.php
[`AbstractBaseMap`]:                                src/AbstractBaseMap.php
[`AbstractBaseCountableMap`]:                       src/AbstractBaseCountableMap.php
[`AbstractRecursiveMapFactory`]:                    src/AbstractRecursiveMapFactory.php
[`CountableMapFactory`]:                            src/CountableMapFactory.php
[`MakeCapableMapTrait`]:                            src/MakeCapableMapTrait.php
[`RecursiveFactoryTrait`]:                          src/RecursiveFactoryTrait.php
