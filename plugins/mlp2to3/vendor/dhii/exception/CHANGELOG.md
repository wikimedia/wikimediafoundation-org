# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.1-alpha5] - 2018-04-02
### Changed
- `_createInternalException()` now only accepts `Exception` instances as `$previous`.

## [0.1-alpha4] - 2018-03-16
### Added
- Factory traits from `dhii/exception-helper-base`, which is now deprecated.

## [0.1-alpha3] - 2018-03-16
### Fixed
- `ExceptionTrait` triggered a fatal error in PHP 5.4.0 due to the order of its traits.

## [0.1-alpha2] - 2018-03-13
### Fixed
- Used to use Dhii `InvalidArgumentException` in some places where the root one should be used.

## [0.1-alpha1] - 2018-03-07
Initial version.
