# CI Detector standalone command

[![Latest Stable Version](https://img.shields.io/packagist/v/ondram/ci-detector-standalone.svg?style=flat-square)](https://packagist.org/packages/ondram/ci-detector-standalone)
[![Build Status](https://img.shields.io/travis/OndraM/ci-detector-standalone.svg?style=flat-square)](https://travis-ci.org/OndraM/ci-detector-standalone)
[![Coverage Status](https://img.shields.io/coveralls/OndraM/ci-detector-standalone/master.svg?style=flat-square)](https://coveralls.io/github/OndraM/ci-detector-standalone?branch=master)
[![License](https://img.shields.io/packagist/l/ondram/ci-detector-standalone.svg?style=flat-square)](https://packagist.org/packages/ondram/ci-detector-standalone)

Standalone CLI command providing unified access to various properties of build environment (like build number, git commit, git branch etc.) 
for many popular CI servers:

 - [Jenkins](https://jenkins.io/)
 - [Travis CI](https://travis-ci.org/)
 - [Bamboo](https://www.atlassian.com/software/bamboo)
 - [CircleCI](https://circleci.com/)
 - [Codeship](https://codeship.com/)
 - [GitLab](https://about.gitlab.com/gitlab-ci/)
 - [TeamCity](https://www.jetbrains.com/teamcity/)
 
If you depend on some of these properties, use the command to make your scripts (and especially CLI tools) portable for 
multiple build environments.

The detection is based on environment variables injected to the build environment by continuous integration 
server. However, these variables are named differently in each CI. This command is based on lightweight 
[ci-detector](https://github.com/OndraM/ci-detector) PHP library, which provides adapter for each supported
CI server.

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

$ ./ci-detector.phar detect git-branch
feature/test

$ ./ci-detector.phar detect git-commit
f45e5809cefdbb819913f9357381f4d291fd49a9

$ ./ci-detector.phar detect repository-url # Not supported on Travis CI, will print empty string


```

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

If you need the detection inside PHP script (and you don't need the CLI command), you can just use directly the lightweight
[ci-detector](https://github.com/OndraM/ci-detector) library.
