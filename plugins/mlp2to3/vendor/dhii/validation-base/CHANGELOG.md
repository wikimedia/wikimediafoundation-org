# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.2-alpha2] - 2018-04-02
### Changed
- Relaxed dependency constraints, allowing future updates.

### Added
- `_createValidationFailedException()` will now default the exception message to the first validation error, if available. 

## [0.2-alpha1] - 2018-03-08
### Added
- `AbstractBaseValidationException`, which serves as a base for simple validation exceptions.
- `ValidatorTrait`, which groups traits commonly used for validation.
- Exception factories.

### Changed
- Exceptions now depend on generic abstract exceptions rather than now obsolete abstract validation exceptions.
- Abstract validators now use traits instead of old abstract classes.

## [0.1] - 2017-03-26
Initial release. Classes and tests included.
