# Dhii - Factory Interface

[![Build Status](https://travis-ci.org/Dhii/factory-interface.svg?branch=develop)](https://travis-ci.org/Dhii/factory-interface)
[![Code Climate](https://codeclimate.com/github/Dhii/factory-interface/badges/gpa.svg)](https://codeclimate.com/github/Dhii/factory-interface)
[![Latest Stable Version](https://poser.pugx.org/dhii/factory-interface/version)](https://packagist.org/packages/dhii/factory-interface)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

Interfaces for working with factories.

## Interfaces
- [`FactoryInterface`][FactoryInterface] - Creates things.
- [`DataObjectFactoryInterface`][DataObjectFactoryInterface] - Creates data objects.
- [`FactoryFactoryInterface`][FactoryFactoryInterface] - Creates factories. For example, could create a factory from
a callback or an FQN.
- [`FactoryAwareInterface`][FactoryAwareInterface] - Exposes a factory.

[Dhii]: https://github.com/Dhii/dhii

[FactoryInterface]:                         src/FactoryInterface.php
[DataObjectFactoryInterface]:               src/DataObjectFactoryInterface.php
[FactoryFactoryInterface]:                  src/FactoryFactoryInterface.php
[FactoryAwareInterface]:                    src/FactoryAwareInterface.php
