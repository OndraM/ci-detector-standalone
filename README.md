# CI detector standalone command

[![Latest Stable Version](https://img.shields.io/packagist/v/ondram/ci-detector-standalone.svg?style=flat-square)](https://packagist.org/packages/ondram/ci-detector-standalone)
[![Build Status](https://img.shields.io/travis/OndraM/ci-detector-standalone.svg?style=flat-square)](https://travis-ci.org/OndraM/ci-detector-standalone)
[![Coverage Status](https://img.shields.io/coveralls/ondram/ci-detector-standalone/master.svg?style=flat-square)](https://coveralls.io/r/ondram/ci-detector-standalone?branch=master)
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

If you need the detection inside PHP script, you can use the [ci-detector](https://github.com/OndraM/ci-detector) library directly. 

## Installation

Install using [Composer](http://getcomposer.org/):

```sh
$ composer require ondram/ci-detector
```

## Example usage

```php
<?php

$ci = OndraM\CiDetector::detect(); // Will return instance implementing CiInterface

// Example outputs when run in Travis
echo $ci->getCiName(); // "Travis CI"
echo $ci->getBuildNumber(); // "35.1"
echo $ci->getBuildUrl(); // https://travis-ci.org/OndraM/ci-detector/jobs/148395137
echo $ci->getGitCommit(); // fad3f7bdbf3515d1e9285b8aa80feeff74507bdd

```

```php
// false is returned from the CiDetector::detect() method if CI server was not detected

$ci = OndraM\CiDetector::detect();
var_dump($ci); // bool(false)

```
