# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.2-alpha1] - 2018-04-11
### Changed
- Completely re-created the DI implementation, which is now largely based on other common pattern implementations,
impements newer standards and techniques.
- Containers still support caching, but the cache itself is injected.
- Still supports lookup delegation, but the composite container is implemented separately.
- Uses traits, and now requires at least PHP 5.4.

## [0.1.1] - 2017-02-03
Non-BC-breaking bugfixes.
Reduced size of dist archive.
Added Gitter notifications of Travis events, and Gitter badge.

### Fixed
- `CompositeContainer#__construct()` not accepting interop containers.
- `CompositeContainer#add()` not implementing interface method.
- `CompositeContainer` not throwing exceptions correctly.

## [0.1] - 2017-02-02
Initial release, containing concrete implementations.

### Added
- Implementations of regular and compound containers, with service provider support.
- Tests.
