# Dhii - Iterator - Interface

[![Build Status](https://travis-ci.org/Dhii/iterator-interface.svg?branch=develop)](https://travis-ci.org/dhii/iterator-interface)
[![Code Climate](https://codeclimate.com/github/Dhii/iterator-interface/badges/gpa.svg)](https://codeclimate.com/github/Dhii/iterator-interface)
[![Test Coverage](https://codeclimate.com/github/Dhii/iterator-interface/badges/coverage.svg)](https://codeclimate.com/github/Dhii/iterator-interface/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/iterator-interface/version)](https://packagist.org/packages/dhii/iterator-interface)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
An iterator standard, which aims to provide more information about each iteration by exposing an immutable iteration
object. This object can be safely passed to other methods, even those which save the reference to it, as a new object
is created for every iteration. That object can provide additional information besides the current key and value.
This makes implementations of this standard easier to work with than other iterator implementations, such as
[`DirectoryIterator`], which expose iteration data through the iterator itself, instead of a separate object, making
it hard to keep iteration data immutable. The fact that its manipulator methods are allowed to throw a specific type
of exception make it possible to create more reliable consumers with meaningful error reporting.

### Features
- All standards-compliant iterators are PHP iterators.
- Iteration information provided via immutable and extensible iteration objects.
- Easier to keep track of iteration information: each iteration produces a separate iteration object.
- Specialized iterator types can be created on interface level by promising custom iteration types.

### Interfaces
- [`IteratorInterface`] - Extends the native [`Iterator`] by exposing a disposable iteration object. Also promises to
throw a specific kind of meaningful exception when rewinding or advancing.
- [`IterationInterface`] - Exposes the key and the value of an iteration.
- [`IteratorAwareInterface`] - Exposes an iterator.
- [`RecursiveIteratorInterface`] - An iterator that iterates over other iterators recursively.
- [`RecursiveIterationInterface`] - An iteration of a recursive iterator. Exposes the depth of the iterator hierarchy,
and the path to the current iteration in that hierarchy.
- [`DepthAwareIterationInterface`] - An iteration that can tell how deep it is in a hierarchy.
- [`PathSegmentsAwareIterationInterface`] - An iteration that can tell the path to itself in a hierarchy.
- [`IteratingExceptionInterface`] - An exception that can occur during iteration.
- [`IterationExceptionInterface`] - An iterating exception that relates to an iteration.
- [`IteratorExceptionInterface`] - An iteration exception that relates to an iterator.


[Dhii]: https://github.com/Dhii/dhii

[`DirectoryIterator`]:                              http://php.net/manual/en/class.directoryiterator.php
[`Iterator`]:                                       http://php.net/manual/en/class.iterator.php

[`IteratorInterface`]:                              src/IteratorInterface.php
[`IterationInterface`]:                             src/IterationInterface.php
[`IteratorAwareInterface`]:                         src/IteratorAwareInterface.php
[`IterationAwareInterface`]:                        src/IterationAwareInterface.php
[`RecursiveIteratorInterface`]:                     src/RecursiveIteratorInterface.php
[`RecursiveIterationInterface`]:                    src/RecursiveIterationInterface.php
[`DepthAwareIterationInterface`]:                   src/DepthAwareIterationInterface.php
[`PathSegmentsAwareIterationInterface`]:            src/PathSegmentsAwareIterationInterface.php
[`IteratingExceptionInterface`]:                    src/Exception/IteratingExceptionInterface.php
[`IterationExceptionInterface`]:                    src/Exception/IterationExceptionInterface.php
[`IteratorExceptionInterface`]:                     src/Exception/IteratorExceptionInterface.php
