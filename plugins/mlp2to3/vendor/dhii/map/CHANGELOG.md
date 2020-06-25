# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.1-alpha7] - 2018-07-30
### Fixed
- Recursive factories used to treat `null` values as sub-maps (#13).

## [0.1-alpha6] - 2018-04-30
### Added
- `CountableMapFactory`.
- `AbstractRecursiveMapFactory`.
- `MakeCapableMapTrait`.
- `RecursiveFactoryTrait`.

### Changed
- Now depending on a newer version of `dhii/collections-interface` for `MapFactoryInterface`.
- Now depending on `dhii/factory-base` for base factory-related implementations, e.g. exceptions and their factories.
- Now depending on `dhii/data-object-abstract` for standards-compliand generic container helper methods.

### Fixed
- Added missing dependency on `dhii/i18n-helper-base`.

## [0.1-alpha5] - 2018-04-25
### Changed
- Depending on newer version of `dhii/iterator-abstract`.
- `AbstractBaseMap` now uses new traits.

## [0.1-alpha4] - 2018-04-23
### Changed
- `AbstractBaseMap#_loop()` now abstracts calculation of key and value.

## [0.1-alpha3] - 2018-04-06
### Changed
- Now depends on newer versions of `dhii/collections-interface`.

## [0.1-alpha2] - 2018-04-06
### Fixed
- Correct version constraint given for `dhii/collections-interface`, thus restoring broken compatibility.

## [0.1-alpha1] - 2018-03-21
Initial version.
