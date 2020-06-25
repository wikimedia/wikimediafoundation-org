# Dhii - Iterator - Helper - Base

[![Build Status](https://travis-ci.org/Dhii/iterator-helper-base.svg?branch=master)](https://travis-ci.org/Dhii/iterator-helper-base)
[![Code Climate](https://codeclimate.com/github/Dhii/iterator-helper-base/badges/gpa.svg)](https://codeclimate.com/github/Dhii/iterator-helper-base)
[![Test Coverage](https://codeclimate.com/github/Dhii/iterator-helper-base/badges/coverage.svg)](https://codeclimate.com/github/Dhii/iterator-helper-base/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/iterator-helper-base/version)](https://packagist.org/packages/dhii/iterator-helper-base)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Common functionality for working with iterables.

[Dhii]: https://github.com/Dhii/dhii

## Traits
- [`ResolveIteratorCapableTrait`] - Retrieves the deepest iterator within a depth limit in an iterator hierarchy,
where every parent is an [`IteratorAggregate`]. Can use a complex test to look for things that are not only iterators.
Detects some signs of infinite recursion.
- [`CountIterableCapableTrait`] - Counts elements in an iterable which passes normalization by
[`NormalizeIterableCapableTrait`]. Uses the most optimal method for retrieving the count.
- [`NormalizeIteratorCapableTrait`] - Normalizes any iterable that passes normalization by
[`NormalizeIterableCapableTrait`] into an instance of [`Iterator`].
- [`MapIterableCapableTrait`][MapIterableCapableTrait] - Maps an iterable, similarly to [`array_map()`][array_map()] or
[`iterator_apply()`][iterator_apply()].


[`ResolveIteratorCapableTrait`]:            src/ResolveIteratorCapableTrait.php
[`CountIterableCapableTrait`]:              src/CountIterableCapableTrait.php
[`NormalizeIteratorCapableTrait`]:          src/NormalizeIteratorCapableTrait.php
[MapIterableCapableTrait]:                  src/MapIterableCapableTrait.php
[`NormalizeIterableCapableTrait`]:          https://github.com/Dhii/normalization-helper-base/blob/develop/src/NormalizeIterableCapableTrait.php

[`Iterator`]:                               http://php.net/manual/en/class.iterator.php
[`IteratorAggregate`]:                      http://php.net/manual/en/class.iteratoraggregate.php
[array_map()]:                              http://php.net/manual/en/function.array-map.php
[iterator_apply()]:                         http://php.net/manual/en/function.iterator-apply.php
