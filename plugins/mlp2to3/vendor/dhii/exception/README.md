# Dhii - Exception

[![Build Status](https://travis-ci.org/Dhii/exception.svg?branch=develop)](https://travis-ci.org/Dhii/exception)
[![Code Climate](https://codeclimate.com/github/Dhii/exception/badges/gpa.svg)](https://codeclimate.com/github/Dhii/exception)
[![Test Coverage](https://codeclimate.com/github/Dhii/exception/badges/coverage.svg)](https://codeclimate.com/github/Dhii/exception/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/exception/version)](https://packagist.org/packages/dhii/exception)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Standards-compliant exception classes.

## Details
This package contains concrete implementations of classes that implement
interfaces in [`dhii/exception-interface`]. This provides developers with
ready-made, standards-compliant classes that can be safely instantiated and
`throw`n to signal the various errors. The concrete exceptions will usually
have a corresponding factory trait, and the factory methods of those traits
are the recommended way of creating new exception instances (after service
definition, of course).

Implementations in this package also have the following features aimed
to become more standards-compliant:

- A [stringable] is accepted everywhere, where a string can be passed.
- All parameters can be passed `null` to signal default value (which may be
not `null`).

Consumers, i.e. code that attempts to `catch`, should not depend on these
classes. Instead, consumers should depend on the interfaces of
[`dhii/exception-interface`][].

## Creating New Exceptions
Sometimes, there is a need to create a new exception class, such as to implement a new standard (like [`dhii/action-interface`][]),
or perhaps to implement two unrelated interfaces (imagine an object that implements both [`Dhii\Action\ActionInterface`][`ActionInterface`]
and  [`Mouf\Utils\Action\ActionInterface`][]). In this case, implementing some of the features of Dhii exceptions may
take an un-necessarily long time. This package provides a way to make creating new exceptions faster.

- If you need to extend a class other than [`Exception`][], then  [`ExceptionTrait`][] helps by combining common
traits used by exceptions, which will initialize the class, while normalizing some values. See [`Dhii\Exception\InvalidArgumentException`][]
for an example.
- If you need to extend the root [`Exception`][], then the quickest way is by instead extending [`Dhii\Exception\AbstractBaseException`][].
See [`Dhii\Exception\Exception`][] for an example. As demonstrated, all basic initialization and normalization can be
achieved by calling `_initBaseException()` in the constructor, after which custom initialization procedure may be added.


[Dhii]:                                             https://github.com/Dhii/dhii
[stringable]:                                       https://github.com/Dhii/stringable-interface

[`dhii/exception-interface`]:                       https://packagist.org/packages/dhii/exception-interface
[`dhii/action-interface`]:                          https://packagist.org/packages/dhii/action-interface

[`Exception`]:                                      http://php.net/manual/en/class.exception.php
[`ExceptionTrait`]:                                 https://github.com/Dhii/exception/blob/develop/src/ExceptionTrait.php
[`ActionInterface`]:                                https://github.com/Dhii/action-interface/blob/develop/src/ActionInterface.php
[`Dhii\Exception\Exception`]:                       https://github.com/Dhii/exception/blob/develop/src/Exception.php
[`Dhii\Exception\AbstractBaseException`]:           https://github.com/Dhii/exception/blob/develop/src/AbstractBaseException.php
[`Dhii\Exception\InvalidArgumentException`]:        https://github.com/Dhii/exception/blob/develop/src/InvalidArgumentException.php
[`Mouf\Utils\Action\ActionInterface`]:              https://github.com/thecodingmachine/utils.action.action-interface/blob/1.0/src/Mouf/Utils/Action/ActionInterface.php
