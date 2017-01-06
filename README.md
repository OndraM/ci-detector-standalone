# CI Detector standalone command

[![Latest Stable Version](https://img.shields.io/packagist/v/ondram/ci-detector-standalone.svg?style=flat-square)](https://packagist.org/packages/ondram/ci-detector-standalone)
[![Build Status](https://img.shields.io/travis/OndraM/ci-detector-standalone.svg?style=flat-square)](https://travis-ci.org/OndraM/ci-detector-standalone)
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

## Installation

Install using [Composer](http://getcomposer.org/):

```sh
$ composer require ondram/ci-detector-standalone
```

If you need the detection inside PHP script, you can use the [ci-detector](https://github.com/OndraM/ci-detector) library directly. 

## Usage

```sh
$ vendor/bin/ci-detector # return status code 0 if CI detected, 1 otherwise 
$ echo $? # to print status code of previous command
0

$ vendor/bin/ci-detector detect ci-name
Travis CI
$ vendor/bin/ci-detector detect build-number
11.3
$ vendor/bin/ci-detector detect build-url
https://travis-ci.org/OndraM/ci-detector-standalone/jobs/189809581
$ vendor/bin/ci-detector detect git-branch
feature/test
$ vendor/bin/ci-detector detect git-commit
f45e5809cefdbb819913f9357381f4d291fd49a9
$ vendor/bin/ci-detector detect repository-url # Not supported on Travis CI, will print empty string

```
