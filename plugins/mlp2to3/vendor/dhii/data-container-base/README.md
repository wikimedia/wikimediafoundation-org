# Dhii - Data - Container - Base

[![Build Status](https://travis-ci.org/Dhii/data-container-base.svg?branch=develop)](https://travis-ci.org/Dhii/data-container-base)
[![Code Climate](https://codeclimate.com/github/Dhii/data-container-base/badges/gpa.svg)](https://codeclimate.com/github/Dhii/data-container-base)
[![Test Coverage](https://codeclimate.com/github/Dhii/data-container-base/badges/coverage.svg)](https://codeclimate.com/github/Dhii/data-container-base/coverage)
[![Latest Stable Version](https://poser.pugx.org/dhii/data-container-base/version)](https://packagist.org/packages/dhii/data-container-base)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]


## Details
This package contains base implementations of standard [container-related interfaces][dhii/data-container-interface],
which includes exceptions, exception factories, and a small opinionated base implementation that demonstrates
simple common usage. Because the standard is an extension of [PSR-11][], this implementation is compliant with PSR-11.

### Classes
- [`AbstractBaseContainer`][AbstractBaseContainer] - A base class for containers. For the simplest implementation, 
only needs a constructor added.
- [`ContainerException`][ContainerException] - Represents a problem with a container.
- [`NotFoundException`][NotFoundException] - Occurs when a key cannot be found in a container.

### Traits
- [`CreateContainerExceptionCapableTrait`][CreateContainerExceptionCapableTrait] - Creates `ContainerException` instances.
- [`CreateNotFoundExceptionCapableTrait`][CreateNotFoundExceptionCapableTrait] - Creates `NotFoundException` instances


[Dhii]:                                             https://github.com/Dhii/dhii
[PSR-11]:                                           https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md

[AbstractBaseContainer]:                            src/AbstractBaseContainer.php
[ContainerException]:                               src/Exception/ContainerException.php
[NotFoundException]:                                src/Exception/NotFoundException.php
[CreateContainerExceptionCapableTrait]:             src/CreateContainerExceptionCapableTrait.php
[CreateNotFoundExceptionCapableTrait]:              src/CreateNotFoundExceptionCapableTrait.php

[dhii/data-container-interface]:                    https://packagist.org/packages/dhii/data-container-interface
