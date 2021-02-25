# CI Detector standalone command

[![Latest Stable Version](https://img.shields.io/packagist/v/ondram/ci-detector-standalone.svg?style=flat-square)](https://packagist.org/packages/ondram/ci-detector-standalone)
[![Build Status](https://img.shields.io/travis/OndraM/ci-detector-standalone.svg?style=flat-square)](https://travis-ci.org/OndraM/ci-detector-standalone)
[![Coverage Status](https://img.shields.io/coveralls/OndraM/ci-detector-standalone/master.svg?style=flat-square)](https://coveralls.io/github/OndraM/ci-detector-standalone?branch=master)

Standalone CLI command wrapper for [ci-detector] PHP library.

It is used to detect continuous integration environment and to provide a unified interface to read the build information.
You can use the command to make your scripts (and especially CLI tools) portable for  multiple build environments.

The detection is based on environment variables injected to the build environment by continuous integration 
server. However, these variables are named differently in each CI. **This command is based on lightweight
[ci-detector] PHP library**, which provides adapter for each supported
CI server.

If you need the detection inside PHP script (so you don't need the CLI command), you can just use directly
the [ci-detector] library.

Releases are matched to the versions of the parent [ci-detector] library.
See [changelog](https://github.com/OndraM/ci-detector/blob/master/CHANGELOG.md) there for the list of the latest changes.

## Usage

```sh
$ ./ci-detector.phar # return status code 0 if CI detected, 1 otherwise
$ echo $? # to print status code of previous command
0

$ ./ci-detector.phar detect ci-name
Travis CI

$ ./ci-detector.phar detect build-number
11.3

$ ./ci-detector.phar detect build-url
https://travis-ci.org/OndraM/ci-detector-standalone/jobs/189809581

$ ./ci-detector.phar detect branch
feature/test

$ ./ci-detector.phar detect commit
f45e5809cefdbb819913f9357381f4d291fd49a9

$ ./ci-detector.phar detect repository-url # Not supported on Travis CI, will print empty string

$ ./ci-detector.phar detect is-pull-request
Yes

$ ./ci-detector.phar detect branch
main
```

See [method reference] for a documentation of each property.

## Dump all available properties

`dump` command will show all properties detected in current environment by ci-detector:

```sh
$ ./ci-detector.phar dump
+-----------------+---------------------------------------------------------------+
| Property name   | Current value                                                 |
+-----------------+---------------------------------------------------------------+
| ci-name         | Travis CI                                                     |
| build-number    | 164.1                                                         |
| build-url       | https://travis-ci.org/OndraM/ci-detector-standalone/jobs/1337 |
| commit          | 9b232f6813915ddb1f226de93366cb924c72e400                      |
| branch          | feature/dump-command                                          |
| target-branch   | main                                                          |
| repository-name | ondram/ci-detector                                            |
| repository-url  | ssh://git@gitserver:7999/project/repo.git                     |
| is-pull-request | Yes                                                           |
+-----------------+---------------------------------------------------------------+
```

This is basically a table with output of `describe()` method of `CiInterface` (see [method reference]).

## Installation

### Install as a standalone PHAR file
CI Detector could be installed as a standalone executable PHAR file (`ci-detector.phar`).
Download latest version from [Releases page](https://github.com/OndraM/ci-detector-standalone/releases/latest).

To run CI Detector use command  `./ci-detector.phar` in the directory where you saved the file (or `php ci-detector.phar` if the
file itself is not executable).

### Install using [Composer](https://getcomposer.org/)

```sh
$ composer require ondram/ci-detector-standalone
```

To run CI Detector use command `vendor/bin/ci-detector`.

If you need the detection inside PHP script (and you don't need the CLI command), you can just use directly
the lightweight [ci-detector] library.

[ci-detector]: https://github.com/OndraM/ci-detector
[method reference]: https://github.com/OndraM/ci-detector#api-methods-reference
