# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.2-alpha1] - 2018-03-07
### Fixed
- Several documentation problems.

### Changed
- Using throwing methods instead of exception factories.
- Using newer versions of `dhii/validation-interface`.
- Using normalization methods in setters where applicable, broadening the range of accepted values.

### Added
- `IsValidCapableTrait`, which separates logic for determining whether something is valid.
- `ValidateCapableTrait`, which separates validation logic.
- Support for validation spec.

### Removed
- Abstract validators.
- Abstract exceptions.

## [0.1] - 2017-03-26
Initial release. Abstract classes and tests included.
