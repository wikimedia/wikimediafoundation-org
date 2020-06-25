# Dhii - Args List Validator

[![Build Status](https://travis-ci.org/Dhii/args-list-validation.svg?branch=develop)](https://travis-ci.org/Dhii/args-list-validation)
[![Code Climate](https://codeclimate.com/github/Dhii/args-list-validation/badges/gpa.svg)](https://codeclimate.com/github/Dhii/args-list-validation)
[![Test Coverage](https://codeclimate.com/github/Dhii/args-list-validation/badges/coverage.svg)](https://codeclimate.com/github/Dhii/args-list-validation/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/args-list-validation/version)](https://packagist.org/packages/dhii/args-list-validation)
[![Latest Unstable Version](https://poser.pugx.org/dhii/args-list-validation/v/unstable)](https://packagist.org/packages/dhii/args-list-validation)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
This package contains functionality for validation of a list of values against a specification. Currently, this
specification is accepted in the form of a list of [`ReflectionParameter`][ReflectionParameter] criteria, which is
typically retrieved via [`ReflectionFunctionAbstract#getParameters()`][ReflectionFunctionAbstract#getParameters()].
Thus, it is possible to validate a list of arguments to determine whether any function or method can be invoked with
it - without actually invoking.

It is also possible to use functionality in this package to determine whether any individual value matches a type
criterion in the form of a [`ReflectionType`][ReflectionType], which is typically retrieved via
[`ReflectionParameter#getType()`][ReflectionParameter#getType()].   

### Traits
- [`GetArgsListErrorsCapableTrait`][GetArgsListErrorsCapableTrait] - Produces a list of reasons for an args list being
invalid against a param spec.
- [`GetValueTypeErrorCapableTrait`][GetValueTypeErrorCapableTrait] - Produces a reason for a value being invalid
against a type criteria.

[Dhii]: https://github.com/Dhii/dhii

[GetArgsListErrorsCapableTrait]:                        src/GetArgsListErrorsCapableTrait.php
[GetValueTypeErrorCapableTrait]:                        src/GetValueTypeErrorCapableTrait.php

[ReflectionParameter]:                                  http://php.net/manual/en/class.reflectionparameter.php
[ReflectionType]:                                       http://php.net/manual/en/class.reflectiontype.php
[ReflectionFunctionAbstract#getParameters()]:           http://php.net/manual/en/reflectionfunctionabstract.getparameters.php
[ReflectionParameter#getType()]:                        http://php.net/manual/en/reflectionparameter.gettype.php
