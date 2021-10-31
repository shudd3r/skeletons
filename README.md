# Shudd3r/Skeletons
[![Latest Stable Version](https://poser.pugx.org/shudd3r/skeletons/version)](https://packagist.org/packages/shudd3r/skeletons)
[![Build status](https://github.com/shudd3r/skeletons/workflows/build/badge.svg)](https://github.com/shudd3r/skeletons/actions)
[![Coverage status](https://coveralls.io/repos/github/shudd3r/skeletons/badge.svg?branch=develop)](https://coveralls.io/github/shudd3r/skeletons?branch=develop)
[![PHP version](https://img.shields.io/packagist/php-v/shudd3r/skeletons.svg)](https://packagist.org/packages/shudd3r/skeletons)
[![LICENSE](https://img.shields.io/github/license/shudd3r/skeletons.svg?color=blue)](LICENSE)
### Template engine for package skeletons

Skeleton packages are used to maintain **consistent structure**
of document (license, readme etc.) and dev environment files
**across multiple packages**. This library allows building
skeleton package scripts with following features:
- Generating package skeleton from template
- Verifying synchronization of existing project with chosen
  template (as a part of [_CI_](https://en.wikipedia.org/wiki/Continuous_integration) process)
- Updating template placeholders in existing project

### Basic Usage
> :warning: See [**shudd3r/skeleton-example**](https://github.com/shudd3r/skeleton-example)
> repository if you got the general idea of using skeleton scripts
> and prefer learning basics from "default example" that uses built-in
> features only and simplified command parsing.

Neither applications nor libraries will use this package directly,
but as a command line tool of the skeleton package they were built
with (dev dependency). To avoid conflicts it is released as a
standalone package that doesn't use any production dependencies,
and php version compatibility is the only limitation.

#### Creating skeleton package
Simplified steps with example script and arbitrary chosen names.
Following sections will cover template & script files in more details.
- Install this library as a dependency of your skeleton package
  using [Composer](https://getcomposer.org/) command:
  ``` bash
  composer require shudd3r/skeletons
  ```
- Add `template` directory with your skeleton template files structure (see: [example](tests/Fixtures/example-files/template))
- Add CLI executable `my-skeleton` script that might look like
  attached [docs/script-example](docs/script-example) file
- Add `"bin"` directive to composer.json pointing to that script
  ``` json
  {
    "bin": ["my-skeleton"]
  }
  ```
- Publish skeleton package

#### Building projects using skeleton
Usage of skeleton package should be explained individually in
its README, because commands may depend on how its executable
script was built.

Scripts of different skeletons may vary significantly as
they're highly customizable. The differences may come from
command & options remapping, using only a subset of built-in
replacements, adding custom ones or introducing custom template
behavior for certain files.
