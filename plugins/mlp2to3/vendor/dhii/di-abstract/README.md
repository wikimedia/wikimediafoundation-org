# Dhii - DI Abstract
[![Build Status](https://travis-ci.org/Dhii/di-abstract.svg?branch=master)](https://travis-ci.org/Dhii/di-abstract)
[![Code Climate](https://codeclimate.com/github/Dhii/di-abstract/badges/gpa.svg)](https://codeclimate.com/github/Dhii/di-abstract)
[![Test Coverage](https://codeclimate.com/github/Dhii/di-abstract/badges/coverage.svg)](https://codeclimate.com/github/Dhii/di-abstract/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/di-abstract/version)](https://packagist.org/packages/dhii/di-abstract)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
Abstract, standards-compliant, yet de-coupled functionality that can be used to create DI containers.

### Traits
- [`AssignDefinitionContainerCapableTrait`][AssignDefinitionContainerCapableTrait] - Allows forcing a service definition
to be invoked with a specific container.
- [`GetServiceCapableCachingTrait`][GetServiceCapableCachingTrait] - Cached retrieval of a resolved service by key.
- [`ResolveDefinitionCapableTrait`][ResolveDefinitionCapableTrait] - Resolution of a service definition.
- [`ServiceCacheAwareTrait`][ServiceCacheAwareTrait] - Awareness of a service cache.


[Dhii]:                                     https://github.com/Dhii/dhii
[SemVer]:                                   http://semver.org/
[caret operator]:                           https://getcomposer.org/doc/articles/versions.md#caret

[AssignDefinitionContainerCapableTrait]:    src/AssignDefinitionContainerCapableTrait.php
[GetServiceCapableCachingTrait]:            src/GetServiceCapableCachingTrait.php
[ResolveDefinitionCapableTrait]:            src/ResolveDefinitionCapableTrait.php
[ServiceCacheAwareTrait]:                   src/ServiceCacheAwareTrait.php
