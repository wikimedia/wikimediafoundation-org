# Dhii - Internationalization Helper Base

[![Build Status](https://travis-ci.org/Dhii/i18n-helper-base.svg?branch=develop)](https://travis-ci.org/Dhii/i18n-helper-base)
[![Code Climate](https://codeclimate.com/github/Dhii/i18n-helper-base/badges/gpa.svg)](https://codeclimate.com/github/Dhii/i18n-helper-base)
[![Test Coverage](https://codeclimate.com/github/Dhii/i18n-helper-base/badges/coverage.svg)](https://codeclimate.com/github/Dhii/i18n-helper-base/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/i18n-helper-base/version)](https://packagist.org/packages/dhii/i18n-helper-base)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

A base for internationalization consumers.

## Traits
- [`StringTranslatingTrait`][StringTranslatingTrait] - Functionality for interpolation and no-op translation of strings.
Is also the base for more complex translation functionality.
- [`StringTranslatorConsumingTrait`][StringTranslatorConsumingTrait] - Functionality for string translation by using a
[`StringTranslatorInterface`][StringTranslatorInterface].
- [`StringTranslatorAwareTrait`][StringTranslatorAwareTrait] - Awareness of a string translator.

[Dhii]: https://github.com/Dhii/dhii

[StringTranslatingTrait]:                       src/StringTranslatingTrait.php
[StringTranslatorConsumingTrait]:               src/StringTranslatorConsumingTrait.php
[StringTranslatorAwareTrait]:                   src/StringTranslatorAwareTrait.php

[StringTranslatorInterface]:                    https://github.com/Dhii/i18n-interface/blob/develop/src/StringTranslatorInterface.php
