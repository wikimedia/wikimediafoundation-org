# Dhii - Key and Value Aware Traits

[![Build Status](https://travis-ci.org/dhii/data-key-value-aware-abstract.svg?branch=develop)](https://travis-ci.org/dhii/data-key-value-aware-abstract)
[![Code Climate](https://codeclimate.com/github/Dhii/data-key-value-aware-abstract/badges/gpa.svg)](https://codeclimate.com/github/Dhii/data-key-value-aware-abstract)
[![Test Coverage](https://codeclimate.com/github/Dhii/data-key-value-aware-abstract/badges/coverage.svg)](https://codeclimate.com/github/Dhii/data-key-value-aware-abstract/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/data-key-value-aware-abstract/version)](https://packagist.org/packages/dhii/data-key-value-aware-abstract)
[![Latest Unstable Version](https://poser.pugx.org/dhii/data-key-value-aware-abstract/v/unstable)](https://packagist.org/packages/dhii/data-key-value-aware-abstract)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Traits for objects that are aware of a key, a value, or both.

## Traits
- [`KeyAwareTrait`] - Allows storage and retrieval of a key. The key can be any scalar value, in which case it will
be [normalized to string][`NormalizeStringCapableTrait`], or a [Stringable][`StringableInterface`] or `null`,
in which case it will be preserved.
- [`ValueAwareTrait`] - Allows storage and retrieval of a value. A value can be anything.
- [`NameAwareTrait`][NameAwareTrait] - Allows storage and retrieval of a name. A name is a string or stringable that
identifies something in a narrow scope.

[Dhii]: https://github.com/Dhii/dhii

[`KeyAwareTrait`]:                      src/KeyAwareTrait.php
[`ValueAwareTrait`]:                    src/ValueAwareTrait.php
[`KeyValueAwareTrait`]:                 src/KeyValueAwareTrait.php
[NameAwareTrait]:                       src/NameAwareTrait.php

[`NormalizeStringCapableTrait`]:        https://github.com/Dhii/normalization-helper-base/blob/develop/src/NormalizeStringCapableTrait.php
[`StringableInterface`]:                https://github.com/Dhii/stringable-interface/blob/develop/src/StringableInterface.php
