# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.2-alpha5] - 2018-04-26
### Added
- `MapFactoryInterface`.

## [0.2-alpha4] - 2018-04-09
### Fixed
- Problem #15, where `AddCapableInterface#$add()` didn't accept the item.

## [0.2-alpha3] - 2018-04-06
### Changed
- `CountableMapInterface` no longer extends `CountableSetInterface`, but still extends `CountableListInterface`.

### Added
- `SetCapableMapInterface`.

## [0.2-alpha2] - 2018-04-06
### Changed
- `SetInterface` no longer extends `HasCapableInterface`, but extends new `HasItemCapableInterface`
- `MapInterface` no longer extends `SetInterface`, but is still traversable.

### Added 
- `HasItemCapableInterface`.
- `AddCapableInterface`.
- `AddCapableSetInterface`.
- `SetCapableInterface`.

## [0.2-alpha1] - 2018-04-06
Initial version.
