# Dhii - Iterator Abstract

[![Build Status](https://travis-ci.org/Dhii/iterator-abstract.svg?branch=develop)](https://travis-ci.org/Dhii/iterator-abstract)
[![Code Climate](https://codeclimate.com/github/Dhii/iterator-abstract/badges/gpa.svg)](https://codeclimate.com/github/Dhii/iterator-abstract)
[![Test Coverage](https://codeclimate.com/github/Dhii/iterator-abstract/badges/coverage.svg)](https://codeclimate.com/github/Dhii/iterator-abstract/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/iterator-abstract/version)](https://packagist.org/packages/dhii/iterator-abstract)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
Functionality for iterators that comply with [dhii/iterator-interface][], or are otherwise based on iteration objects.

### Traits
- [`IteratorTrait`] - Abstract functionality for creating iterators based on an iteration object.
- [`TrackingIteratorTrait`] - An iterator specialization for iterators that use a tracker to track the iterator position.
- [`IteratorIteratorTrait`] - A tracking iterator specialization that uses an internal [`Iterator`][].

[Dhii]:                                                     https://github.com/Dhii/dhii
[dhii/iterator-interface]:                                  https://packagist.org/packages/dhii/iterator-interface

[`Iterator`]:                                               http://php.net/manual/en/class.iterator.php

[`IteratorTrait`]:                                          src/IteratorTrait.php
[`TrackingIteratorTrait`]:                                  src/TrackingIteratorTrait.php
[`IteratorIteratorTrait`]:                                  src/IteratorIteratorTrait.php
