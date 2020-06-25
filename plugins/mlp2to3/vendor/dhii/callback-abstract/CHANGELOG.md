# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.1-alpha5] - 2018-04-04
### Fixed
- Issue #10, where context was lost inside invoked closures.

## [0.1-alpha4] - 2018-04-03
### Fixed
- Issue #8, where `_invokeCallable()` would not invoke callable objects.

## [0.1-alpha3] - 2018-04-01
### Added
- `CreateReflectionForCallableCapableTrait`.
- `NormalizeCallableCapableTrait`.
- `NormalizeMethodCallableCapableTrait`.
- `ValidateParamsCapableTrait`.

### Changed
- `InvokeCallableCapableTrait` now throws an `InternalExceptionInterface` for all exceptions that occur while actually
invoking the callable. Specifically, this applies to exceptions thrown inside the callable.
- `InvokeCallableCapableTrait` now throws an `InvocationExceptionInterface` if the callable cannot be invoked.
Specifically, this applies if the arguments do not match the signature of the callable.
- `ArgsAwareTrait#_getArgs()` now returns an empty list by default.
- List of arguments is now allowed to be an `stdClass` object, as this is a valid iterable. 
- `InvokeCallbackCapableTrait` no longer needs `_createInvalidArgumentException()`.

## [0.1-alpha2] - 2018-02-12
### Fixed
- Exception type throw by `InvokeCallableCapableTrait#_invokeCallable()` is now properly documented to be
`InvocationExceptionInterface` instead of `RootException`.

### Changed
- `_setArgs()` and `_invokeCallback()` now normalize the args list, instead of manually validating it.
- `_setArgs()`, `_invokeCallback()`, and `_invokeCallable()` now accept `stdClass` as arg list.

## [0.1-alpha1] - 2018-02-12
Initial version.
