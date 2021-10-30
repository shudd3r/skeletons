# Shudd3r/Skeletons
[![Latest Stable Version](https://poser.pugx.org/shudd3r/skeletons/version)](https://packagist.org/packages/shudd3r/skeletons)
[![Build status](https://github.com/shudd3r/skeletons/workflows/build/badge.svg)](https://github.com/shudd3r/skeletons/actions)
[![Coverage status](https://coveralls.io/repos/github/shudd3r/skeletons/badge.svg?branch=develop)](https://coveralls.io/github/shudd3r/skeletons?branch=develop)
[![PHP version](https://img.shields.io/packagist/php-v/shudd3r/skeletons.svg)](https://packagist.org/packages/shudd3r/skeletons)
[![LICENSE](https://img.shields.io/github/license/shudd3r/skeletons.svg?color=blue)](LICENSE)
### Template engine for package skeletons

This library allows building skeleton package scripts with
following features:
- Generating package skeleton from template
- Updating placeholders in existing project
- Verifying synchronization of existing project with template

Neither applications nor libraries will use this package directly,
but as a command line tool of the skeleton package they were built
with (dev dependency). To avoid conflicts this is released as a
standalone package that doesn't use any production dependencies,
and php version compatibility is the only limitation.

### Creating skeleton package - Basics
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
