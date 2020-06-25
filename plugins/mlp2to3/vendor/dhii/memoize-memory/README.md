# Dhii - Memoize - Memory

[![Build Status](https://travis-ci.org/Dhii/memoize-memory.svg?branch=develop)](https://travis-ci.org/Dhii/memoize-memory)
[![Code Climate](https://codeclimate.com/github/Dhii/memoize-memory/badges/gpa.svg)](https://codeclimate.com/github/Dhii/memoize-memory)
[![Test Coverage](https://codeclimate.com/github/Dhii/memoize-memory/badges/coverage.svg)](https://codeclimate.com/github/Dhii/memoize-memory/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/memoize-memory/version)](https://packagist.org/packages/dhii/memoize-memory)
[![Latest Unstable Version](https://poser.pugx.org/dhii/memoize-memory/v/unstable)](https://packagist.org/packages/dhii/memoize-memory)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]


## Details
An in-memory memoizer implementation, compatible with the [`dhii/simple-cache-interface`][dhii/simple-cache-interface]
standard.

### Classes
- [`MemoryMemoizer`][MemoryMemoizer] - A simple concrete [`SimpleCacheInterface`][SimpleCacheInterface] implementation
that persists in memory for a single runtime.
- [`AbstractBaseContainerMemory`][AbstractBaseContainerMemory] - Base functionality for
[`Dhii\Cache\ContainerInterface`][Dhii\Cache\ContainerInterface] implementations, which is enough for most memoizing needs.
- [`AbstractBaseSimpleCacheMemory`][AbstractBaseSimpleCacheMemory] - Base functionality for complete
[`SimpleCacheInterface`][SimpleCacheInterface] implementations, which provides additional methods of controlling cache.

[Dhii]:                                     https://github.com/Dhii/dhii
[dhii/simple-cache-interface]:              https://packagist.org/packages/dhii/simple-cache-interface

[AbstractBaseContainerMemory]:              src/AbstractBaseContainerMemory.php
[AbstractBaseSimpleCacheMemory]:            src/AbstractBaseSimpleCacheMemory.php
[MemoryMemoizer]:                           src/MemoryMemoizer.php

[SimpleCacheInterface]:                     https://github.com/Dhii/simple-cache-interface/blob/develop/src/SimpleCacheInterface.php
[Dhii\Cache\ContainerInterface]:            https://github.com/Dhii/simple-cache-interface/blob/develop/src/ContainerInterface.php
