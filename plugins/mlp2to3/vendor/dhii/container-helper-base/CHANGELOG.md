# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.1-alpha9] - 2018-11-06
### Fixed
- `_containerHas()` used `isset()`, which caused false negatives when retrieving `null` values (#28).

## [0.1-alpha8] - 2018-07-30
### Fixed
- `_containerGet()` used `isset()`, which caused exception when retrieving `null` values (#26).

## [0.1-alpha7] - 2018-04-10
### Added
- `ContainerGetPathCapableTrait`.
- `ContainerSetPathCapableTrait`.
- `ContainerListHasCapableTrait`.
- `ContainerListGetCapableTrait`.

## [0.1-alpha6] - 2018-03-03
### Added
- Exception messages related to keys now include the key for convenience.

## [0.1-alpha5] - 2018-02-16
### Fixed
- Bugs where the Dhii container was being checked for, instead of the PSR one.
- Methods were missing `@throws` docs for validation exceptions.

### Added
- Phan config.
- `ContainerSetCapableTrait`, `ContainerSetManyCapableTrait`, `ContainerUnsetCapableTrait`,
`ContainerUnsetManyCapableTrait`, and `NormalizeKeyCapableTrait`.

### Changed
- The `ArrayAccess` API is explicitly used where appropriate.
- `_containerGet()` and `_containerHas()` no longer do container normalization.
- Using the new `_normalizeKey()` for key normalization, instead of `_normalizeString()`.

## [0.1-alpha4] - 2018-02-01
### Fixed
- `_normalizeContainer()` used to allow `null` containers.

### Added
- `NormalizeWritableContainerCapableTrait` for writable container normalization.

## [0.1-alpha3] - 2018-02-01
### Added
- `NormalizeContainerCapableTrait` for container normalization.

## [0.1-alpha2] - 2018-01-31
### Changed
- Catering for more exceptional cases, specifically with `ArrayAccess`.
- Preserving original key in exceptions, where possible and applicable.
- Improved tests.
- Fixed code standards.
- Simplified some code.
- Documentation for types, trows, and other fixed.

## [0.1-alpha1] - 2018-01-31
Initial version.
