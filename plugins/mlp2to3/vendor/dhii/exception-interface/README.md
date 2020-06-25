# Dhii - Exception - Interface

[![Build Status](https://travis-ci.org/Dhii/exception-interface.svg?branch=master)](https://travis-ci.org/Dhii/exception-interface)
[![Code Climate](https://codeclimate.com/github/Dhii/exception-interface/badges/gpa.svg)](https://codeclimate.com/github/Dhii/exception-interface)
[![Test Coverage](https://codeclimate.com/github/Dhii/exception-interface/badges/coverage.svg)](https://codeclimate.com/github/Dhii/exception-interface/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/exception-interface/version)](https://packagist.org/packages/dhii/exception-interface)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Interfaces for most common exceptions.

## Details
- [`ThrowableInterface`] - The base of all exception interfaces. Declares
all the same methods as `Exception` in a compatible way, and additionally
extends [`StringableInterface`].
- [`BadSubjectExceptionInterface`] - Base interface for all exceptions that
are related to some value being invalid, malformed, out of range, etc. Exposes
that value via `getSubject()`.
- [`InvalidArgumentExceptionInterface`] - Allows de-coupling from the vanilla
[`InvalidArgumentException`], and makes it more useful besides signalling the
problem type by exposing the argument via `BadSubjectExceptionInterface#getSubejct()`.
- [`ArgumentCodeAwareInterface`] - Useful with `InvalidArgumentExceptionInterface`
for providing information about the source of the problematic argument.
- [`OutOfBoundsExceptionInterface`] - Complementing the native [`OutOfBoundsException`], it occurs when a key is
addressed in a set that does not have it, like accessing a non-existing array key. Exposes the bad key.
- [`OutOfRangeExceptionInterface`] - Complementing the native [`OutOfRangeException`], it occurs when a value is
valid but illegal, i.e. is outside of the allowed range, like when an integer that represents a colour and must be
0-255 has the value of 256. Exposes the bad value.
- [`RuntimeExceptionInterface`][RuntimeExceptionInterface] - A generic runtime problem.
- [`InternalExceptionInterface`][InternalExceptionInterface] - A problem that occurs in relation to the inner workings
of the unit, and is not caused by the consumer. Guarantees to expose an inner exception.


[`ThrowableInterface`]:                 src/ThrowableInterface.php
[`BadSubjectExceptionInterface`]:       src/BadSubjectExceptionInterface.php
[`InvalidArgumentExceptionInterface`]:  src/InvalidArgumentExceptionInterface.php
[`ArgumentCodeAwareInterface`]:         src/ArgumentCodeAwareInterface.php
[`OutOfBoundsExceptionInterface`]:      src/OutOfBoundsExceptionInterface.php
[`OutOfRangeExceptionInterface`]:       src/OutOfRangeExceptionInterface.php
[RuntimeExceptionInterface]:            src/RuntimeExceptionInterface.php
[InternalExceptionInterface]:           src/InternalExceptionInterface.php
[`StringableInterface`]:                https://github.com/Dhii/stringable-interface/blob/master/src/StringableInterface.php
[`InvalidArgumentException`]:           http://php.net/manual/en/class.invalidargumentexception.php
[`OutOfBoundsException`]:               http://php.net/manual/en/class.outofboundsexception.php
[`OutOfRangeException`]:                http://php.net/manual/en/class.outofrangeexception.php

[Dhii]: https://github.com/Dhii/dhii
