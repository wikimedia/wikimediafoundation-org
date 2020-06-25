# Dhii - Collections Interface

[![Build Status](https://travis-ci.org/Dhii/collections-interface.svg?branch=develop)](https://travis-ci.org/Dhii/collections-interface)
[![Code Climate](https://codeclimate.com/github/Dhii/collections-interface/badges/gpa.svg)](https://codeclimate.com/github/Dhii/collections-interface)
[![Test Coverage](https://codeclimate.com/github/Dhii/collections-interface/badges/coverage.svg)](https://codeclimate.com/github/Dhii/collections-interface/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/collections-interface/version)](https://packagist.org/packages/dhii/collections-interface)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

A highly [ISP][ISP]-compliant collection of interfaces that represent collections.

## Interfaces
- [`CountableListInterface`][CountableListInterface]: A list that can be iterated and counted.
- [`HasItemCapableInterface`][HasItemCapableInterface]: Something that can be checked for the existence of an item.
- [`AddCapableInterface`][AddCapableInterface]: Something that can have an item added.
- [`SetCapableInterface`][SetCapableInterface]: Something that can set a value for a key.
- [`SetInterface`][SetInterface]: A list that can be checked for a value.
- [`AddCapableSetInterface`][AddCapableSetInterface]: A set that can have an item added.
- [`CountableSetInterface`][CountableSetInterface]: A set that can be counted.
- [`MapInterface`][MapInterface]: An iterable container.
- [`CountableMapInterface`][CountableMapInterface]: A countable map.
- [`SetCapableMapInterface`][SetCapableMapInterface]: A map that can have a value set for a key.
- [`MapFactoryInterface`]: A factory of `MapInterface` objects.


[Dhii]: https://github.com/Dhii/dhii
[ISP]: https://en.wikipedia.org/wiki/Interface_segregation_principle

[CountableListInterface]:                           src/CountableListInterface.php
[SetInterface]:                                     src/SetInterface.php
[CountableSetInterface]:                            src/CountableSetInterface.php
[MapInterface]:                                     src/MapInterface.php
[CountableMapInterface]:                            src/CountableMapInterface.php
[AddCapableInterface]:                              src/AddCapableInterface.php
[HasItemCapableInterface]:                          src/HasItemCapableInterface.php
[AddCapableSetInterface]:                           src/AddCapableSetInterface.php
[SetCapableInterface]:                              src/SetCapableInterface.php
[SetCapableMapInterface]:                           src/SetCapableMapInterface.php
[`MapFactoryInterface`]:                            src/MapFactoryInterface.php
