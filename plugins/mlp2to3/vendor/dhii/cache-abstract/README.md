# Dhii - Cache - Abstract

[![Build Status](https://travis-ci.org/Dhii/cache-abstract.svg?branch=develop)](https://travis-ci.org/Dhii/cache-abstract)
[![Code Climate](https://codeclimate.com/github/Dhii/cache-abstract/badges/gpa.svg)](https://codeclimate.com/github/Dhii/cache-abstract)
[![Test Coverage](https://codeclimate.com/github/Dhii/cache-abstract/badges/coverage.svg)](https://codeclimate.com/github/Dhii/cache-abstract/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/cache-abstract/version)](https://packagist.org/packages/dhii/cache-abstract)
[![Latest Unstable Version](https://poser.pugx.org/dhii/cache-abstract/v/unstable)](https://packagist.org/packages/dhii/cache-abstract)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
Abstract functionality for caching.

### Traits
- [`GetCachedCapableTrait`][GetCachedCapableTrait] - Retrieves a cached value by key from self. Supports default value or
value generation. Very useful for memoization. Allows specifying a TTL, if the underlying storage medium supports that.

[Dhii]: https://github.com/Dhii/dhii

[GetCachedCapableTrait]:                            src/GetCachedCapableTrait.php
