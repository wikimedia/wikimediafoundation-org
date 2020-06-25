## Dhii - Validation Abstract ##
[![Build Status](https://travis-ci.org/Dhii/validation-abstract.svg?branch=master)](https://travis-ci.org/Dhii/validation-abstract)
[![Code Climate](https://codeclimate.com/github/Dhii/validation-abstract/badges/gpa.svg)](https://codeclimate.com/github/Dhii/validation-abstract)
[![Test Coverage](https://codeclimate.com/github/Dhii/validation-abstract/badges/coverage.svg)](https://codeclimate.com/github/Dhii/validation-abstract/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/validation-abstract/version)](https://packagist.org/packages/dhii/validation-abstract)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Common abstract functionality for validation.

### Traits
- [`ValidateCapableTrait`][ValidateCapableTrait] - Functionality for validation. Throws a
[`ValidationFailedExceptionInterface`][ValidationFailedExceptionInterface] if validation errors are detected.
- [`IsValidCapableTrait`][IsValidCapableTrait] - Determines whether a subject is valid in a boolean way.
- [`GetValidationErrorsCapableCompositeTrait`][GetValidationErrorsCapableCompositeTrait] - Uses a list of child validators
to produce a list of errors.
- [`ChildValidatorsAwareTrait`][ChildValidatorsAwareTrait] - Awareness of child validators.
- [`ValidatorAwareTrait`][ValidatorAwareTrait] - Awareness of a validator.
- [`SpecAwareTrait`][SpecAwareTrait] - Awareness of a validation specification. Useful for validators geared to validate
against a spec.
- [`ValidationSubjectAwareTrait`][ValidationSubjectAwareTrait] - Awareness of a validation subject, i.e. what is being validated.
- [`ValidationErrorsAwareTrait`][ValidationErrorsAwareTrait] - Awareness of a list of validation errors, i.e. reasons
for validation failure.

[Dhii]: https://github.com/Dhii/dhii

[ValidateCapableTrait]:                         src/ValidateCapableTrait.php
[IsValidCapableTrait]:                          src/IsValidCapableTrait.php
[GetValidationErrorsCapableCompositeTrait]:     src/GetValidationErrorsCapableCompositeTrait.php
[ChildValidatorsAwareTrait]:                    src/ChildValidatorsAwareTrait.php
[ValidatorAwareTrait]:                          src/ValidatorAwareTrait.php
[SpecAwareTrait]:                               src/SpecAwareTrait.php
[ValidationSubjectAwareTrait]:                  src/ValidationSubjectAwareTrait.php
[ValidationErrorsAwareTrait]:                   src/ValidationErrorsAwareTrait.php

[ValidationFailedExceptionInterface]:           https://github.com/Dhii/validation-interface/blob/develop/src/Exception/ValidationFailedExceptionInterface.php
